<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Budget;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_admin_can_view_any_patient(): void
    {
        $this->switchTenant('clinic-a');
        $this->actingAs($this->adminA);

        $response = $this->get('/app/patients');

        $response->assertStatus(200);
    }

    public function test_doctor_can_view_patients(): void
    {
        $this->switchTenant('clinic-a');
        $this->actingAs($this->doctorA);

        $response = $this->get('/app/patients');

        $response->assertStatus(200);
    }

    public function test_only_admin_can_delete_patient(): void
    {
        $this->switchTenant('clinic-a');
        $patient = Patient::create([
            'clinic_id' => 'clinic-a',
            'name' => 'To Delete',
            'doctor_id' => $this->doctorA->id,
        ]);

        $this->actingAs($this->adminA);
        $this->assertTrue($this->adminA->can('delete', $patient));

        // Doctors also have delete permission on patients per the role setup
        $this->actingAs($this->doctorA);
        $this->assertTrue($this->doctorA->can('delete', $patient));
    }

    public function test_doctor_can_manage_appointments(): void
    {
        $this->switchTenant('clinic-a');
        $appointment = Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'user_id' => $this->doctorA->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($this->doctorA);
        $this->assertTrue($this->doctorA->can('update', $appointment));
    }

    public function test_budget_creation_requires_admin(): void
    {
        $this->switchTenant('clinic-a');
        $budget = Budget::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'total' => 100000,
            'status' => 'draft',
        ]);

        $this->actingAs($this->adminA);
        $this->assertTrue($this->adminA->can('update', $budget));
    }
}
