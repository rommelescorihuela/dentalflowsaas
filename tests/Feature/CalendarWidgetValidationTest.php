<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Filament\App\Widgets\CalendarWidget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class CalendarWidgetValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $doctor;
    protected Patient $patient;
    protected Appointment $appointment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
        $this->switchTenant('clinic-a');

        $this->doctor = $this->doctorA;

        $this->patient = Patient::create([
            'clinic_id' => tenant('id'),
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'rut' => '12345678-9',
        ]);

        $this->appointment = Appointment::create([
            'clinic_id' => tenant('id'),
            'patient_id' => $this->patient->id,
            'start_time' => now()->addDays(5)->setTime(10, 0),
            'end_time' => now()->addDays(5)->setTime(10, 30),
            'status' => 'scheduled',
            'type' => 'control',
        ]);
    }

    public function test_cannot_reschedule_to_past_date(): void
    {
        $widget = app(CalendarWidget::class);

        $pastStart = now()->subDays(2)->setTime(10, 0)->toIso8601String();
        $pastEnd = now()->subDays(2)->setTime(10, 30)->toIso8601String();

        $widget->updateAppointment($this->appointment->id, $pastStart, $pastEnd);

        $this->appointment->refresh();

        $this->assertNotEquals(Carbon::parse($pastStart), $this->appointment->start_time);
    }

    public function test_cannot_reschedule_to_overlapping_slot(): void
    {
        Appointment::create([
            'clinic_id' => tenant('id'),
            'patient_id' => $this->patient->id,
            'start_time' => now()->addDays(10)->setTime(14, 0),
            'end_time' => now()->addDays(10)->setTime(14, 30),
            'status' => 'scheduled',
            'type' => 'control',
        ]);

        $widget = app(CalendarWidget::class);

        $overlapStart = now()->addDays(10)->setTime(14, 15)->toIso8601String();
        $overlapEnd = now()->addDays(10)->setTime(14, 45)->toIso8601String();

        $widget->updateAppointment($this->appointment->id, $overlapStart, $overlapEnd);

        $this->appointment->refresh();

        $this->assertNotEquals(Carbon::parse($overlapStart), $this->appointment->start_time);
    }

    public function test_can_reschedule_to_valid_future_slot(): void
    {
        $widget = app(CalendarWidget::class);

        $newStart = now()->addDays(15)->setTime(11, 0)->toIso8601String();
        $newEnd = now()->addDays(15)->setTime(11, 30)->toIso8601String();

        $widget->updateAppointment($this->appointment->id, $newStart, $newEnd);

        $this->appointment->refresh();

        $this->assertEquals(Carbon::parse($newStart), $this->appointment->start_time);
        $this->assertEquals(Carbon::parse($newEnd), $this->appointment->end_time);
    }

    public function test_does_not_fail_for_nonexistent_appointment(): void
    {
        $widget = app(CalendarWidget::class);

        $futureStart = now()->addDays(20)->setTime(9, 0)->toIso8601String();
        $futureEnd = now()->addDays(20)->setTime(9, 30)->toIso8601String();

        $widget->updateAppointment(99999, $futureStart, $futureEnd);

        $this->assertTrue(true);
    }

    public function test_overlapping_check_ignores_cancelled_appointments(): void
    {
        Appointment::create([
            'clinic_id' => tenant('id'),
            'patient_id' => $this->patient->id,
            'start_time' => now()->addDays(8)->setTime(15, 0),
            'end_time' => now()->addDays(8)->setTime(15, 30),
            'status' => 'cancelled',
            'type' => 'control',
        ]);

        $widget = app(CalendarWidget::class);

        $newStart = now()->addDays(8)->setTime(15, 0)->toIso8601String();
        $newEnd = now()->addDays(8)->setTime(15, 30)->toIso8601String();

        $widget->updateAppointment($this->appointment->id, $newStart, $newEnd);

        $this->appointment->refresh();

        $this->assertEquals(Carbon::parse($newStart), $this->appointment->start_time);
    }

    public function test_get_events_returns_appointments_in_date_range(): void
    {
        $widget = app(CalendarWidget::class);

        $events = $widget->getEvents();

        $this->assertIsArray($events);
        $this->assertGreaterThan(0, count($events));
        $this->assertEquals($this->appointment->id, $events[0]['id']);
    }
}
