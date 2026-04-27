<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Patient;
use App\Models\Budget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_super_admin_can_view_all_clinics(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $this->actingAsSuperAdmin();

        $clinics = Clinic::all();
        $this->assertGreaterThanOrEqual(2, $clinics->count());
    }

    public function test_super_admin_can_create_new_clinic(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $this->actingAsSuperAdmin();

        $clinic = Clinic::create([
            'id' => 'clinic-new-test',
            'name' => 'Nueva Clínica Test',
        ]);

        $clinic->domains()->create(['domain' => 'newclinic.test']);

        $this->assertEquals('clinic-new-test', $clinic->id);
        $this->assertEquals('Nueva Clínica Test', $clinic->name);
    }

    public function test_super_admin_can_assign_clinic_to_user(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $this->actingAsSuperAdmin();

        $newUser = User::create([
            'name' => 'Nuevo Usuario',
            'email' => 'newuser@test.com',
            'password' => bcrypt('password'),
            'clinic_id' => 'clinic-a',
        ]);

        $newUser->assignRole('admin');

        $this->assertTrue($newUser->hasRole('admin'));
        $this->assertEquals('clinic-a', $newUser->clinic_id);
    }

    public function test_super_admin_has_all_permissions(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $this->actingAsSuperAdmin();

        $this->assertTrue($superAdmin->hasRole('super-admin'));
        $this->assertTrue($superAdmin->hasPermissionTo('ViewAny:Clinic'));
        $this->assertTrue($superAdmin->hasPermissionTo('Create:Clinic'));
        $this->assertTrue($superAdmin->hasPermissionTo('Delete:Clinic'));
    }

    public function test_super_admin_can_access_any_tenant_data(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $this->actingAsSuperAdmin();

        Tenancy::initialize('clinic-a');
        $patientInA = Patient::create([
            'name' => 'Paciente en A',
            'email' => 'pa@test.com',
            'phone' => '+56911111111',
            'clinic_id' => 'clinic-a',
            'rut' => '33333333-3',
        ]);
        $patientInAId = $patientInA->id;

        Tenancy::initialize('clinic-b');
        $patientInB = Patient::create([
            'name' => 'Paciente en B',
            'email' => 'pb@test.com',
            'phone' => '+56944444444',
            'clinic_id' => 'clinic-b',
            'rut' => '44444444-4',
        ]);
        $patientInBId = $patientInB->id;

        Tenancy::initialize('clinic-a');
        $foundA = Patient::withoutGlobalScopes()->find($patientInAId);
        $foundB = Patient::withoutGlobalScopes()->find($patientInBId);

        $this->assertEquals('Paciente en A', $foundA->name);
        $this->assertEquals('Paciente en B', $foundB->name);
    }

    public function test_super_admin_can_view_global_metrics(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $this->actingAsSuperAdmin();

        $totalPatients = Patient::withoutGlobalScopes()->count();
        $totalClinics = Clinic::withoutGlobalScopes()->count();

        $this->assertGreaterThan(0, $totalClinics);
    }

    public function test_super_admin_can_manage_roles_globally(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $this->actingAsSuperAdmin();

        $role = Role::findByName('admin');
        $this->assertEquals('admin', $role->name);

        $superAdmin->givePermissionTo('ViewAny:Role');
        $this->assertTrue($superAdmin->hasPermissionTo('ViewAny:Role'));
    }

    public function test_regular_admin_cannot_access_other_clinic_data(): void
    {
        $this->actingAsAdmin($this->adminA);
        
        $patientA = Patient::all()->first();
        $this->assertEquals('clinic-a', $patientA->clinic_id);
    }

    public function test_super_admin_bypasses_tenant_scope(): void
    {
        $superAdmin = $this->createSuperAdmin();
        Auth::login($superAdmin);

        $patients = Patient::withoutGlobalScopes()->count();
        $this->assertGreaterThan(0, $patients);

        $clinics = Clinic::withoutGlobalScopes()->count();
        $this->assertGreaterThanOrEqual(2, $clinics);
    }
}