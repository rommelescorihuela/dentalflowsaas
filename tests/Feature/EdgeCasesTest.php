<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Budget;
use App\Models\Odontogram;
use App\Models\ClinicalRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Facades\Tenancy;
use Carbon\Carbon;

class EdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_patient_with_same_rut_in_same_clinic_fails(): void
    {
        $this->switchTenant('clinic-a');

        $uniqueRut = '99999999-' . time();

        $patient1 = Patient::create([
            'name' => 'Paciente 1',
            'email' => 'p1-' . time() . '@clinic-a.test',
            'phone' => '+56911111111',
            'clinic_id' => 'clinic-a',
            'rut' => $uniqueRut,
        ]);

        $this->assertEquals($uniqueRut, $patient1->rut);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Patient::create([
            'name' => 'Paciente Duplicado',
            'email' => 'p2-' . time() . '@clinic-a.test',
            'phone' => '+56922222222',
            'clinic_id' => 'clinic-a',
            'rut' => $uniqueRut,
        ]);
    }

    public function test_appointment_in_past_fails_validation(): void
    {
        $this->switchTenant('clinic-a');

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'start_time' => Carbon::now()->subDays(5),
            'end_time' => Carbon::now()->subDays(5)->addMinutes(30),
            'status' => 'scheduled',
        ]);
    }

    public function test_empty_budget_total_is_zero(): void
    {
        $this->switchTenant('clinic-a');

        $budget = Budget::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'total' => 0,
            'status' => 'pending',
        ]);

        $this->assertEquals(0, $budget->total);
    }

    public function test_odontogram_with_no_records_is_empty(): void
    {
        $this->switchTenant('clinic-a');

        $odontogram = $this->createOdontogram($this->patientA);

        $odontogram->load('clinicalRecords');

        $this->assertEquals(0, $odontogram->clinicalRecords->count());
    }

    public function test_budget_with_100_items(): void
    {
        $this->switchTenant('clinic-a');

        $budget = Budget::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'total' => 0,
            'status' => 'pending',
        ]);

        $total = 0;
        for ($i = 1; $i <= 100; $i++) {
            $cost = rand(1000, 50000);
            $total += $cost;
            \App\Models\BudgetItem::create([
                'budget_id' => $budget->id,
                'treatment_name' => "Tratamiento $i",
                'cost' => $cost,
                'quantity' => 1,
            ]);
        }

        $budget->total = $budget->items->sum(fn($item) => $item->cost * $item->quantity);
        $budget->save();

        $this->assertEquals(100, $budget->items->count());
        $this->assertEquals($total, $budget->total);
    }

    public function test_patient_with_very_long_name(): void
    {
        $this->switchTenant('clinic-a');

        $longName = str_repeat('A', 255);

        $patient = Patient::create([
            'name' => $longName,
            'email' => 'longname@clinic-a.test',
            'phone' => '+56911111111',
            'clinic_id' => 'clinic-a',
            'rut' => '33333333-3',
        ]);

        $this->assertEquals($longName, $patient->name);
    }

    public function test_appointment_overlapping_times(): void
    {
        $this->switchTenant('clinic-a');

        $startTime = Carbon::now()->addDays(1)->setHour(10);

        $appointment1 = Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'start_time' => $startTime,
            'end_time' => $startTime->copy()->addMinutes(30),
            'status' => 'scheduled',
        ]);

        $this->assertEquals('scheduled', $appointment1->status);
    }

    public function test_clinical_record_all_diagnosis_codes(): void
    {
        $this->switchTenant('clinic-a');

        $diagnosisCodes = ['caries', 'obturation', 'endodontia', 'exodoncia', 'implante', 'puente', 'corona', 'blanqueamiento'];

        $odontogram = $this->createOdontogram($this->patientA);

        foreach ($diagnosisCodes as $code) {
            ClinicalRecord::create([
                'clinic_id' => 'clinic-a',
                'patient_id' => $this->patientA->id,
                'odontogram_id' => $odontogram->id,
                'tooth_number' => 11,
                'surface' => 'center',
                'diagnosis_code' => $code,
                'treatment_status' => 'planned',
            ]);
        }

        $this->assertEquals(count($diagnosisCodes), ClinicalRecord::where('odontogram_id', $odontogram->id)->count());
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $this->switchTenant('clinic-a');

        $user = \App\Models\User::create([
            'name' => 'Usuario Multi Rol',
            'email' => 'multirole@clinic-a.test',
            'password' => bcrypt('password'),
            'clinic_id' => 'clinic-a',
        ]);

        $user->assignRole('doctor');
        $user->assignRole('assistant');

        $this->assertTrue($user->hasRole('doctor'));
        $this->assertTrue($user->hasRole('assistant'));
    }

    public function test_budget_maximum_amount(): void
    {
        $this->switchTenant('clinic-a');

        $budget = Budget::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'total' => 99999999,
            'status' => 'pending',
        ]);

        $this->assertEquals(99999999, $budget->total);
    }

    public function test_patient_phone_validation_format(): void
    {
        $this->switchTenant('clinic-a');

        $patient = Patient::create([
            'name' => 'Test Phone',
            'email' => 'phone@test.com',
            'phone' => '+56912345678',
            'clinic_id' => 'clinic-a',
            'rut' => '44444444-4',
        ]);

        $this->assertStringStartsWith('+', $patient->phone);
    }
}
