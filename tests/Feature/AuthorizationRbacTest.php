<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Patient;
use App\Models\Odontogram;
use App\Models\ClinicalRecord;
use App\Models\Budget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AuthorizationRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_user_can_view_patients_with_permission(): void
    {
        $this->switchTenant('clinic-a');
        
        $patient = Patient::create([
            'name' => 'Test Patient',
            'email' => 'test@clinic-a.test',
            'phone' => '+56911111111',
            'clinic_id' => 'clinic-a',
            'rut' => '11111111-1',
        ]);

        $this->actingAsDoctor($this->doctorA);

        $foundPatient = Patient::find($patient->id);
        $this->assertNotNull($foundPatient);
    }

    public function test_odontogram_belongs_to_correct_patient(): void
    {
        $this->switchTenant('clinic-a');
        
        $odontogram = Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Odontograma Test',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        $this->assertEquals($this->patientA->id, $odontogram->patient_id);
        $this->assertEquals('clinic-a', $odontogram->clinic_id);
    }

    public function test_clinical_record_enforces_tenant(): void
    {
        $this->switchTenant('clinic-a');
        
        $odontogram = Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Odontograma Test',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        $record = ClinicalRecord::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'odontogram_id' => $odontogram->id,
            'tooth_number' => 11,
            'surface' => 'center',
            'diagnosis_code' => 'caries',
            'treatment_status' => 'planned',
        ]);

        $this->switchTenant('clinic-b');
        
        $recordFromClinicA = ClinicalRecord::find($record->id);
        $this->assertNull($recordFromClinicA, 'No debe acceder a registros de otra clínica');
    }

    public function test_budget_enforces_tenant_isolation(): void
    {
        $this->switchTenant('clinic-a');
        
        $budget = Budget::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'total' => 100000,
            'status' => 'pending',
        ]);

        $this->switchTenant('clinic-b');
        
        $budgetFromClinicA = Budget::find($budget->id);
        $this->assertNull($budgetFromClinicA, 'No debe acceder a presupuestos de otra clínica');
    }

    public function test_odontogram_prevents_cross_tenant_access(): void
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
        
        $odontogramAFromB = Odontogram::find($odontogramA->id);
        $this->assertNull($odontogramAFromB);
    }

    public function test_patient_data_isolation_verified(): void
    {
        $this->switchTenant('clinic-a');
        
        Patient::create([
            'name' => 'Paciente A1',
            'email' => 'pa1@clinic-a.test',
            'phone' => '+56911111111',
            'clinic_id' => 'clinic-a',
            'rut' => '11111111-1',
        ]);
        
        Patient::create([
            'name' => 'Paciente A2',
            'email' => 'pa2@clinic-a.test',
            'phone' => '+56911111112',
            'clinic_id' => 'clinic-a',
            'rut' => '11111112-2',
        ]);

        $this->switchTenant('clinic-b');
        
        Patient::create([
            'name' => 'Paciente B1',
            'email' => 'pb1@clinic-b.test',
            'phone' => '+56922222221',
            'clinic_id' => 'clinic-b',
            'rut' => '22222221-1',
        ]);

        $this->switchTenant('clinic-a');
        $patientsInA = Patient::count();
        $this->assertEquals(3, $patientsInA); // 1 from setUp + 2 new

        $this->switchTenant('clinic-b');
        $patientsInB = Patient::count();
        $this->assertEquals(2, $patientsInB); // 1 from setUp + 1 new
    }

    public function test_doctor_can_access_own_clinic_data(): void
    {
        $this->switchTenant('clinic-a');
        
        $patient = Patient::create([
            'name' => 'Paciente Doctor A',
            'email' => 'pda@clinic-a.test',
            'phone' => '+56911111111',
            'clinic_id' => 'clinic-a',
            'rut' => '11111111-1',
        ]);

        $this->actingAsDoctor($this->doctorA);
        
        $found = Patient::find($patient->id);
        $this->assertNotNull($found);
        $this->assertEquals('clinic-a', $found->clinic_id);
    }

    public function test_clinic_scope_prevents_data_leakage(): void
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
        
        $budgets = Budget::all();
        $this->assertTrue($budgets->every(fn($b) => $b->clinic_id === 'clinic-a'));
        $this->assertFalse($budgets->contains('id', $budgetB->id));
    }
}
