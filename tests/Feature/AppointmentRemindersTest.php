<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

class AppointmentRemindersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
        Mail::fake();
    }

    public function test_reminder_sent_for_tomorrow_appointment(): void
    {
        Tenancy::initialize('clinic-a');
        setPermissionsTeamId('clinic-a');

        $appointment = Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'user_id' => $this->doctorA->id,
            'start_time' => now()->addDay()->setHour(10)->setMinute(0),
            'end_time' => now()->addDay()->setHour(10)->setMinute(30),
            'status' => 'scheduled',
            'type' => 'consultation',
        ]);

        $this->artisan('appointments:send-reminders')
            ->assertSuccessful()
            ->expectsOutputToContain('Recordatorios enviados: 1');

        Mail::assertSent(\App\Mail\AppointmentReminder::class, function ($mail) use ($appointment) {
            return $mail->appointment->id === $appointment->id
                && $mail->hasTo($this->patientA->email);
        });
    }

    public function test_no_reminder_for_today_appointment(): void
    {
        Tenancy::initialize('clinic-a');
        setPermissionsTeamId('clinic-a');

        Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'user_id' => $this->doctorA->id,
            'start_time' => now()->addHours(2)->setMinute(0),
            'end_time' => now()->addHours(2)->setMinute(30),
            'status' => 'scheduled',
            'type' => 'consultation',
        ]);

        $this->artisan('appointments:send-reminders')
            ->assertSuccessful()
            ->expectsOutputToContain('Recordatorios enviados: 0');

        Mail::assertNothingSent();
    }

    public function test_no_reminder_for_cancelled_appointment(): void
    {
        Tenancy::initialize('clinic-a');
        setPermissionsTeamId('clinic-a');

        Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'user_id' => $this->doctorA->id,
            'start_time' => now()->addDay()->setHour(10)->setMinute(0),
            'end_time' => now()->addDay()->setHour(10)->setMinute(30),
            'status' => 'cancelled',
            'type' => 'consultation',
        ]);

        $this->artisan('appointments:send-reminders')
            ->assertSuccessful()
            ->expectsOutputToContain('Recordatorios enviados: 0');

        Mail::assertNothingSent();
    }

    public function test_no_reminder_for_patient_without_email(): void
    {
        Tenancy::initialize('clinic-a');
        setPermissionsTeamId('clinic-a');

        $patientNoEmail = \App\Models\Patient::create([
            'name' => 'Sin Email',
            'phone' => '+56999999999',
            'clinic_id' => 'clinic-a',
        ]);

        Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $patientNoEmail->id,
            'user_id' => $this->doctorA->id,
            'start_time' => now()->addDay()->setHour(10)->setMinute(0),
            'end_time' => now()->addDay()->setHour(10)->setMinute(30),
            'status' => 'scheduled',
            'type' => 'consultation',
        ]);

        $this->artisan('appointments:send-reminders')
            ->assertSuccessful()
            ->expectsOutputToContain('omitidos (sin email): 1');

        Mail::assertNothingSent();
    }

    public function test_reminder_only_for_scheduled_status(): void
    {
        Tenancy::initialize('clinic-a');
        setPermissionsTeamId('clinic-a');

        // Cita para mañana con status 'completed' - no debe enviar recordatorio
        Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'user_id' => $this->doctorA->id,
            'start_time' => now()->addDay()->setHour(10)->setMinute(0),
            'end_time' => now()->addDay()->setHour(10)->setMinute(30),
            'status' => 'completed',
            'type' => 'consultation',
        ]);

        $this->artisan('appointments:send-reminders')
            ->assertSuccessful()
            ->expectsOutputToContain('Recordatorios enviados: 0');

        Mail::assertNothingSent();
    }
}
