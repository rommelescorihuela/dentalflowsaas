<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Odontogram;
use App\Models\ClinicalRecord;
use App\Models\Budget;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            'rut' => '50000001-' . time(),
        ]);

        $this->actingAsDoctor($this->doctorA);

        $foundPatient = Patient::find($patient->id);
        $this->assertEquals('Test Patient', $foundPatient->name);
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

    public function test_doctor_can_access_own_clinic_data(): void
    {
        $this->switchTenant('clinic-a');

        $patient = Patient::create([
            'name' => 'Paciente Doctor A',
            'email' => 'pda@clinic-a.test',
            'phone' => '+56911111111',
            'clinic_id' => 'clinic-a',
            'rut' => '50000001-' . time(),
        ]);

        $this->actingAsDoctor($this->doctorA);

        $found = Patient::find($patient->id);
        $this->assertEquals('Paciente Doctor A', $found->name);
        $this->assertEquals('clinic-a', $found->clinic_id);
    }

    public function test_admin_permissions_differ_from_doctor(): void
    {
        $this->actingAsAdmin($this->adminA);

        $this->assertTrue($this->adminA->hasPermissionTo('Create:Clinic'));
        $this->assertTrue($this->adminA->hasPermissionTo('Create:User'));
        $this->assertFalse($this->doctorA->hasPermissionTo('Create:Clinic'));
        $this->assertFalse($this->doctorA->hasPermissionTo('Create:User'));
    }

    public function test_assistant_cannot_delete_resources(): void
    {
        $this->actingAsAssistant($this->assistantA);

        $this->assertFalse($this->assistantA->hasPermissionTo('Delete:Patient'));
        $this->assertFalse($this->assistantA->hasPermissionTo('Delete:Budget'));
        $this->assertFalse($this->assistantA->hasPermissionTo('Delete:Appointment'));
    }

    public function test_doctor_can_manage_clinical_records(): void
    {
        $this->actingAsDoctor($this->doctorA);

        $this->assertTrue($this->doctorA->hasPermissionTo('Create:ClinicalRecord'));
        $this->assertTrue($this->doctorA->hasPermissionTo('Update:ClinicalRecord'));
    }

    public function test_super_admin_has_all_permissions(): void
    {
        $superAdmin = $this->createSuperAdmin();

        $this->assertTrue($superAdmin->hasRole('super-admin'));
        $this->assertTrue($superAdmin->hasPermissionTo('ViewAny:Clinic'));
        $this->assertTrue($superAdmin->hasPermissionTo('Delete:Clinic'));
        $this->assertTrue($superAdmin->hasPermissionTo('Create:User'));
    }
}
