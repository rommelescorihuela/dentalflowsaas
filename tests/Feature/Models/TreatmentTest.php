<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Appointment;
use App\Models\Treatment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
            'name' => 'Root Canal',
            'code' => 'D3310',
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
            'code' => 'T001',
        ]);

        $this->assertEquals(1, Treatment::count());
    }
}
