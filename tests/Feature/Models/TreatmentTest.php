<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Treatment;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TreatmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_treatment_belongs_to_appointment(): void
    {
        $appointment = Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'status' => 'scheduled',
        ]);

        $treatment = Treatment::create([
            'clinic_id' => 'clinic-a',
            'appointment_id' => $appointment->id,
            'name' => 'Root canal treatment',
        ]);

        $this->assertEquals($appointment->id, $treatment->appointment->id);
    }

    public function test_treatment_has_clinic_scope(): void
    {
        $appointment = Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
        ]);

        Treatment::create([
            'clinic_id' => 'clinic-a',
            'appointment_id' => $appointment->id,
            'name' => 'Test treatment',
        ]);

        $this->assertEquals(1, Treatment::count());
    }
}
