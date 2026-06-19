<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

class PortalIdorSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected ?Patient $patientA2 = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();

        // Segundo paciente en la misma clínica (clinic-a) para testear IDOR intra-clínica
        Tenancy::initialize('clinic-a');
        setPermissionsTeamId('clinic-a');

        $this->patientA2 = Patient::create([
            'name' => 'Paciente A2',
            'email' => 'paciente-a2@clinic-a.test',
            'phone' => '+56933333333',
            'clinic_id' => 'clinic-a',
            'rut' => '33333333-3',
        ]);
    }

    public function test_patient_can_view_own_budget(): void
    {
        $budget = $this->createBudgetWithItems($this->patientA, 'sent');

        Tenancy::initialize('clinic-a');

        $url = URL::signedRoute('portal.budgets.view', [
            'tenant' => 'clinic-a',
            'patient' => $this->patientA->id,
            'budget' => $budget->id,
        ]);

        $response = $this->get($url);

        $response->assertStatus(200);
    }

    public function test_patient_cannot_view_budget_of_other_patient_same_clinic(): void
    {
        $budgetA = $this->createBudgetWithItems($this->patientA, 'sent');
        $budgetA2 = $this->createBudgetWithItems($this->patientA2, 'sent');

        Tenancy::initialize('clinic-a');

        // Patient A tries to access Patient A2's budget (same clinic)
        $url = URL::signedRoute('portal.budgets.view', [
            'tenant' => 'clinic-a',
            'patient' => $this->patientA->id,
            'budget' => $budgetA2->id,
        ]);

        $response = $this->get($url);

        $response->assertStatus(403);
    }

    public function test_patient_can_accept_own_budget(): void
    {
        $budget = $this->createBudgetWithItems($this->patientA, 'sent');

        Tenancy::initialize('clinic-a');

        $url = URL::signedRoute('portal.budgets.accept', [
            'tenant' => 'clinic-a',
            'patient' => $this->patientA->id,
            'budget' => $budget->id,
        ]);

        $response = $this->post($url);

        $response->assertStatus(302);
        $this->assertEquals('accepted', $budget->fresh()->status);
    }

    public function test_patient_cannot_accept_budget_of_other_patient_same_clinic(): void
    {
        $budgetA = $this->createBudgetWithItems($this->patientA, 'sent');
        $budgetA2 = $this->createBudgetWithItems($this->patientA2, 'sent');

        Tenancy::initialize('clinic-a');

        $url = URL::signedRoute('portal.budgets.accept', [
            'tenant' => 'clinic-a',
            'patient' => $this->patientA->id,
            'budget' => $budgetA2->id,
        ]);

        $response = $this->post($url);

        $response->assertStatus(403);
        $this->assertEquals('sent', $budgetA2->fresh()->status);
    }

    public function test_patient_cannot_reject_budget_of_other_patient_same_clinic(): void
    {
        $budgetA = $this->createBudgetWithItems($this->patientA, 'sent');
        $budgetA2 = $this->createBudgetWithItems($this->patientA2, 'sent');

        Tenancy::initialize('clinic-a');

        $url = URL::signedRoute('portal.budgets.reject', [
            'tenant' => 'clinic-a',
            'patient' => $this->patientA->id,
            'budget' => $budgetA2->id,
        ]);

        $response = $this->post($url);

        $response->assertStatus(403);
        $this->assertEquals('sent', $budgetA2->fresh()->status);
    }

    public function test_patient_can_download_own_budget_pdf(): void
    {
        $budget = $this->createBudgetWithItems($this->patientA, 'sent');

        Tenancy::initialize('clinic-a');

        $url = URL::signedRoute('portal.budgets.pdf', [
            'tenant' => 'clinic-a',
            'patient' => $this->patientA->id,
            'budget' => $budget->id,
        ]);

        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_patient_cannot_download_pdf_of_other_patient_same_clinic(): void
    {
        $budgetA = $this->createBudgetWithItems($this->patientA, 'sent');
        $budgetA2 = $this->createBudgetWithItems($this->patientA2, 'sent');

        Tenancy::initialize('clinic-a');

        $url = URL::signedRoute('portal.budgets.pdf', [
            'tenant' => 'clinic-a',
            'patient' => $this->patientA->id,
            'budget' => $budgetA2->id,
        ]);

        $response = $this->get($url);

        $response->assertStatus(403);
    }
}
