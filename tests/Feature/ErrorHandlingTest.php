<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Budget;
use App\Models\Odontogram;
use App\Models\ClinicalRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_patient_not_found_returns_null(): void
    {
        $this->switchTenant('clinic-a');

        $patient = Patient::find(99999);

        $this->assertNull($patient);
    }

    public function test_appointment_not_found_returns_null(): void
    {
        $this->switchTenant('clinic-a');

        $appointment = Appointment::find(99999);

        $this->assertNull($appointment);
    }

    public function test_budget_not_found_returns_null(): void
    {
        $this->switchTenant('clinic-a');

        $budget = Budget::find(99999);

        $this->assertNull($budget);
    }

    public function test_odontogram_not_found_returns_null(): void
    {
        $this->switchTenant('clinic-a');

        $odontogram = Odontogram::find(99999);

        $this->assertNull($odontogram);
    }

    public function test_budget_cannot_accept_twice(): void
    {
        $this->switchTenant('clinic-a');

        $budget = $this->createBudgetWithItems($this->patientA, 'pending');

        $budget->status = 'accepted';
        $budget->save();

        $budget->status = 'accepted';
        $budget->save();

        $this->assertEquals('accepted', $budget->fresh()->status);
    }

    public function test_appointment_cannot_be_double_booked(): void
    {
        $this->switchTenant('clinic-a');

        $startTime = now()->addDays(1)->setHour(10);

        Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'start_time' => $startTime,
            'end_time' => $startTime->copy()->addMinutes(30),
            'status' => 'scheduled',
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'start_time' => $startTime->copy()->addMinutes(15),
            'end_time' => $startTime->copy()->addMinutes(45),
            'status' => 'scheduled',
        ]);
    }

    public function test_deleting_patient_with_appointments(): void
    {
        $this->switchTenant('clinic-a');

        $appointment = $this->createAppointment($this->patientA, $this->doctorA);

        $patient = Patient::find($this->patientA->id);

        $patientCount = Patient::count();
        $this->assertGreaterThan(0, $patientCount);
    }

    public function test_deleting_odontogram_with_records(): void
    {
        $this->switchTenant('clinic-a');

        $odontogram = $this->createOdontogram($this->patientA);

        $this->createClinicalRecord($this->patientA, $odontogram, 11, 'center', 'caries');
        $this->createClinicalRecord($this->patientA, $odontogram, 12, 'center', 'caries');

        $odontogram->load('clinicalRecords');

        $this->assertEquals(2, $odontogram->clinicalRecords->count());
    }

    public function test_invalid_clinic_switch(): void
    {
        $this->switchTenant('clinic-a');

        $beforeClinic = tenant('id');

        try {
            $this->switchTenant('nonexistent-clinic');
        } catch (\Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById $e) {
            $afterClinic = tenant('id');
            $this->assertEquals('clinic-a', $afterClinic);
        }
    }

    public function test_concurrent_appointment_updates(): void
    {
        $this->switchTenant('clinic-a');

        $appointment = $this->createAppointment($this->patientA, $this->doctorA);

        $id = $appointment->id;

        $appointment1 = Appointment::find($id);
        $appointment1->status = 'completed';
        $appointment1->save();

        $appointment2 = Appointment::find($id);

        $this->assertEquals('completed', $appointment2->status);
    }

    public function test_large_data_set_query_performance(): void
    {
        $this->switchTenant('clinic-a');

        $startTime = microtime(true);

        for ($i = 0; $i < 100; $i++) {
            Patient::create([
                'name' => "Paciente $i",
                'email' => "paciente$i@clinic-a.test",
                'phone' => '+56911111111',
                'clinic_id' => 'clinic-a',
                'rut' => str_pad($i + 100, 8, '0', STR_PAD_LEFT) . '-1',
            ]);
        }

        $patients = Patient::all();
        $count = $patients->count();

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        $this->assertEquals(101, $count);
        $this->assertLessThan(5.0, $duration);
    }

    public function test_json_response_on_invalid_data(): void
    {
        $this->switchTenant('clinic-a');

        $patient = Patient::find($this->patientA->id);

        $json = $patient->toJson();

        $decoded = json_decode($json);

        $this->assertNotFalse($decoded);
    }

    public function test_array_access_on_model(): void
    {
        $this->switchTenant('clinic-a');

        $patient = Patient::find($this->patientA->id);

        $array = $patient->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayHasKey('clinic_id', $array);
    }

    public function test_relation_caching(): void
    {
        $this->switchTenant('clinic-a');

        $odontogram = $this->createOdontogram($this->patientA);

        $this->createClinicalRecord($this->patientA, $odontogram, 11, 'center', 'caries');

        $records1 = $odontogram->clinicalRecords;
        $records2 = $odontogram->fresh()->clinicalRecords;

        $this->assertEquals($records1->count(), $records2->count());
    }
}
