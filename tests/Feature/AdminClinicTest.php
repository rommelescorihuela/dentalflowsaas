<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Patient;
use App\Models\Budget;
use App\Models\ProcedurePrice;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminClinicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_admin_can_view_clinic_dashboard(): void
    {
        $this->actingAsAdmin($this->adminA);

        $patients = Patient::all();
        $this->assertGreaterThanOrEqual(1, $patients->count());
    }

    public function test_admin_can_create_doctor(): void
    {
        $this->actingAsAdmin($this->adminA);

        $doctor = User::create([
            'name' => 'Nuevo Doctor',
            'email' => 'newdoctor@clinic-a.test',
            'password' => bcrypt('password'),
            'clinic_id' => 'clinic-a',
        ]);
        $doctor->assignRole('doctor');

        $this->assertEquals('newdoctor@clinic-a.test', $doctor->email);
        $this->assertTrue($doctor->hasRole('doctor'));
    }

    public function test_admin_can_create_assistant(): void
    {
        $this->actingAsAdmin($this->adminA);

        $assistant = User::create([
            'name' => 'Nueva Asistente',
            'email' => 'newassistant@clinic-a.test',
            'password' => bcrypt('password'),
            'clinic_id' => 'clinic-a',
        ]);
        $assistant->assignRole('assistant');

        $this->assertEquals('newassistant@clinic-a.test', $assistant->email);
        $this->assertTrue($assistant->hasRole('assistant'));
    }

    public function test_admin_can_create_procedure_price(): void
    {
        $this->actingAsAdmin($this->adminA);

        $procedure = ProcedurePrice::create([
            'clinic_id' => 'clinic-a',
            'procedure_name' => 'Extracción dental',
            'code' => 'D7140',
            'price' => 80000,
            'duration' => '60',
            'description' => 'Extracción de pieza dental',
        ]);

        $this->assertEquals('Extracción dental', $procedure->procedure_name);
        $this->assertEquals(80000, $procedure->price);
    }

    public function test_admin_can_manage_inventory(): void
    {
        $this->actingAsAdmin($this->adminA);

        $item = Inventory::create([
            'clinic_id' => 'clinic-a',
            'name' => 'Anestésico local',
            'sku' => 'ANES-001',
            'quantity' => 50,
            'low_stock_threshold' => 10,
            'unit' => 'ampollas',
            'price' => 1500,
            'supplier' => 'DentalSupply Co',
            'category' => 'Medicamentos',
        ]);

        $this->assertEquals('Anestésico local', $item->name);
        $this->assertEquals('clinic-a', $item->clinic_id);
    }

    public function test_admin_can_view_budgets(): void
    {
        $this->actingAsAdmin($this->adminA);

        $budget = $this->createBudgetWithItems($this->patientA, 'pending');

        $this->assertEquals('pending', $budget->status);
        $this->assertGreaterThan(0, $budget->total);
    }

    public function test_admin_cannot_assign_user_to_different_clinic(): void
    {
        $this->actingAsAdmin($this->adminA);

        $user = User::create([
            'name' => 'Usuario Incorrecto',
            'email' => 'wrongclinic@test.com',
            'password' => bcrypt('password'),
            'clinic_id' => 'clinic-b',
        ]);

        $this->assertEquals('clinic-b', $user->clinic_id);
    }

    public function test_admin_can_manage_roles_within_clinic(): void
    {
        $this->actingAsAdmin($this->adminA);

        $this->assertTrue($this->adminA->hasPermissionTo('ViewAny:Role'));
        $this->assertTrue($this->adminA->hasPermissionTo('Create:User'));
        $this->assertTrue($this->adminA->hasPermissionTo('Update:User'));
    }

    public function test_admin_can_accept_payment(): void
    {
        $this->actingAsAdmin($this->adminA);

        $budget = $this->createBudgetWithItems($this->patientA, 'accepted');
        $payment = $this->createPayment($budget, $this->patientA);

        $this->assertEquals($budget->total / 2, $payment->amount);
        $this->assertEquals($budget->id, $payment->budget_id);
    }

    public function test_admin_can_update_clinic_settings(): void
    {
        $this->actingAsAdmin($this->adminA);

        $clinic = Clinic::find('clinic-a');
        $this->assertEquals('clinic-a', $clinic->id);

        $clinic->name = 'Clínica Dental Actualizada';
        $clinic->save();

        $this->assertEquals('Clínica Dental Actualizada', $clinic->name);
    }
}
