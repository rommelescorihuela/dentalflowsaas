<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Patient;
use App\Models\Budget;
use App\Models\Odontogram;
use App\Models\ClinicalRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Facades\Tenancy;

class SecurityTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_patient_isolation_by_clinic(): void
    {
        $this->switchTenant('clinic-a');
        $patients = Patient::all();

        $this->assertCount(1, $patients);
        $this->assertEquals('Paciente A', $patients->first()->name);
    }

    public function test_cannot_access_patient_from_other_clinic(): void
    {
        $this->switchTenant('clinic-b');
        $patientBId = $this->patientB->id;

        $this->switchTenant('clinic-a');

        $patientFromClinicA = Patient::where('id', $patientBId)->first();
        $this->assertNull(
            $patientFromClinicA,
            'No debe encontrar paciente de otra clínica'
        );
    }

    public function test_odontogram_isolation_by_clinic(): void
    {
        $this->switchTenant('clinic-a');
        $odontogramA = Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Odontograma A',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        $this->switchTenant('clinic-b');
        $odontogramB = Odontogram::create([
            'clinic_id' => 'clinic-b',
            'patient_id' => $this->patientB->id,
            'name' => 'Odontograma B',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        $this->switchTenant('clinic-a');
        $odontogramsInA = Odontogram::all();
        $this->assertCount(1, $odontogramsInA);
        $this->assertEquals('Odontograma A', $odontogramsInA->first()->name);
    }

    public function test_clinical_record_isolation_by_clinic(): void
    {
        $this->switchTenant('clinic-a');
        $odontogramA = Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Odontograma A',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        $recordA = ClinicalRecord::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'odontogram_id' => $odontogramA->id,
            'tooth_number' => 11,
            'surface' => 'center',
            'diagnosis_code' => 'caries',
            'treatment_status' => 'planned',
        ]);

        $this->switchTenant('clinic-b');
        $odontogramB = Odontogram::create([
            'clinic_id' => 'clinic-b',
            'patient_id' => $this->patientB->id,
            'name' => 'Odontograma B',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        $recordB = ClinicalRecord::create([
            'clinic_id' => 'clinic-b',
            'patient_id' => $this->patientB->id,
            'odontogram_id' => $odontogramB->id,
            'tooth_number' => 21,
            'surface' => 'center',
            'diagnosis_code' => 'filled',
            'treatment_status' => 'completed',
        ]);

        $this->switchTenant('clinic-a');
        $recordsInA = ClinicalRecord::all();
        $this->assertCount(1, $recordsInA);
        $this->assertEquals('caries', $recordsInA->first()->diagnosis_code);
    }

    public function test_budget_isolation_by_clinic(): void
    {
        $this->switchTenant('clinic-a');
        $budgetA = Budget::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'total' => 100000,
            'status' => 'pending',
        ]);

        $this->switchTenant('clinic-b');
        $budgetB = Budget::create([
            'clinic_id' => 'clinic-b',
            'patient_id' => $this->patientB->id,
            'total' => 200000,
            'status' => 'accepted',
        ]);

        $this->switchTenant('clinic-a');
        $budgetsInA = Budget::all();
        $this->assertCount(1, $budgetsInA);
        $this->assertEquals(100000, $budgetsInA->first()->total);
    }

    public function test_cannot_query_clinic_b_records_from_clinic_a(): void
    {
        $this->switchTenant('clinic-a');

        $budgetIdFromClinicB = Budget::where('clinic_id', 'clinic-b')->first()?->id ?? 999;

        $budget = Budget::where('id', $budgetIdFromClinicB)->first();
        $this->assertNull($budget, 'No debe encontrar presupuesto de otra clínica');
    }

    public function test_user_belongs_to_correct_clinic(): void
    {
        $this->switchTenant('clinic-a');
        $this->assertEquals('clinic-a', $this->doctorA->clinic_id);

        $this->switchTenant('clinic-b');
        $this->assertEquals('clinic-b', $this->doctorB->clinic_id);
    }

    public function test_tenant_context_isolation(): void
    {
        $this->switchTenant('clinic-a');
        $tenantIdA = tenant('id');

        $this->switchTenant('clinic-b');
        $tenantIdB = tenant('id');

        $this->switchTenant('clinic-a');
        $tenantIdAfterSwitch = tenant('id');

        $this->assertEquals('clinic-a', $tenantIdA);
        $this->assertEquals('clinic-b', $tenantIdB);
        $this->assertEquals('clinic-a', $tenantIdAfterSwitch);
    }

    public function test_global_scopes_isolate_queries(): void
    {
        $this->switchTenant('clinic-a');
        Patient::create([
            'name' => 'Paciente A2',
            'email' => 'paciente-a2@clinic-a.test',
            'phone' => '+56911111112',
            'clinic_id' => 'clinic-a',
            'rut' => '11111112-2',
        ]);

        $this->switchTenant('clinic-b');
        Patient::create([
            'name' => 'Paciente B2',
            'email' => 'paciente-b2@clinic-b.test',
            'phone' => '+56922222221',
            'clinic_id' => 'clinic-b',
            'rut' => '22222221-1',
        ]);

        $this->switchTenant('clinic-a');
        $patients = Patient::all();
        $this->assertCount(2, $patients);
        $this->assertTrue($patients->every(fn($p) => $p->clinic_id === 'clinic-a'));
    }
}