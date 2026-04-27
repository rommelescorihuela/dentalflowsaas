<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Patient;
use App\Models\Budget;
use App\Models\Appointment;
use App\Models\Odontogram;
use App\Models\ClinicalRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class HttpApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_patient_portal_dashboard_returns_200(): void
    {
        $this->switchTenant('clinic-a');

        $url = URL::signedRoute('portal.dashboard', ['tenant' => 'clinic-a', 'patient' => $this->patientA->id]);

        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertViewIs('patient-portal.dashboard');
        $response->assertViewHas('patient', fn($patient) => $patient->id === $this->patientA->id);
    }

    public function test_patient_portal_dashboard_returns_403_for_unsigned_url(): void
    {
        $this->switchTenant('clinic-a');

        $response = $this->get("/clinic-a/portal/{$this->patientA->id}");

        $response->assertStatus(403);
    }

    public function test_patient_portal_returns_404_for_invalid_patient(): void
    {
        $this->switchTenant('clinic-a');

        $url = URL::signedRoute('portal.dashboard', ['tenant' => 'clinic-a', 'patient' => 99999]);

        $response = $this->get($url);

        $response->assertStatus(404);
    }

    public function test_accept_budget_returns_redirect(): void
    {
        $this->switchTenant('clinic-a');

        $budget = $this->createBudgetWithItems($this->patientA, 'pending');

        $url = URL::signedRoute('portal.budgets.accept', ['tenant' => 'clinic-a', 'budget' => $budget->id]);

        $response = $this->post($url);

        $response->assertRedirect();
        $this->assertEquals('accepted', $budget->fresh()->status);
    }

    public function test_accept_budget_from_wrong_clinic_returns_404(): void
    {
        $this->switchTenant('clinic-a');

        $budget = $this->createBudgetWithItems($this->patientA, 'pending');
        $budgetId = $budget->id;

        $this->switchTenant('clinic-b');

        $url = URL::signedRoute('portal.budgets.accept', ['tenant' => 'clinic-b', 'budget' => $budgetId]);

        $response = $this->post($url);

        $response->assertStatus(404);
    }

    public function test_login_redirects_to_admin_login(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    public function test_register_page_returns_200(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_admin_login_page_returns_200(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    public function test_patient_portal_shows_appointments(): void
    {
        $this->switchTenant('clinic-a');

        $this->createAppointment($this->patientA, $this->doctorA);

        $url = URL::signedRoute('portal.dashboard', ['tenant' => 'clinic-a', 'patient' => $this->patientA->id]);
        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertViewHas('patient', function ($patient) {
            return $patient->appointments->count() >= 1;
        });
    }

    public function test_patient_portal_shows_budgets(): void
    {
        $this->switchTenant('clinic-a');

        $this->createBudgetWithItems($this->patientA, 'pending');

        $url = URL::signedRoute('portal.dashboard', ['tenant' => 'clinic-a', 'patient' => $this->patientA->id]);
        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertViewHas('patient', function ($patient) {
            return $patient->budgets->count() >= 1;
        });
    }

    public function test_patient_portal_shows_clinical_records(): void
    {
        $this->switchTenant('clinic-a');

        $odontogram = $this->createOdontogram($this->patientA);
        $this->createClinicalRecord($this->patientA, $odontogram);

        $url = URL::signedRoute('portal.dashboard', ['tenant' => 'clinic-a', 'patient' => $this->patientA->id]);
        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertViewHas('patient', function ($patient) {
            return $patient->clinicalRecords->count() >= 1;
        });
    }

    public function test_homepage_returns_200(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_patient_portal_with_clinic_b_patient(): void
    {
        $this->switchTenant('clinic-b');

        $url = URL::signedRoute('portal.dashboard', ['tenant' => 'clinic-b', 'patient' => $this->patientB->id]);
        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertViewHas('patient', fn($patient) => $patient->id === $this->patientB->id);
    }

    public function test_budget_accept_changes_status_via_http(): void
    {
        $this->switchTenant('clinic-a');

        $budget = $this->createBudgetWithItems($this->patientA, 'pending');
        $this->assertEquals('pending', $budget->status);

        $url = URL::signedRoute('portal.budgets.accept', ['tenant' => 'clinic-a', 'budget' => $budget->id]);
        $this->post($url);

        $this->assertEquals('accepted', $budget->fresh()->status);
    }

    public function test_patient_portal_returns_correct_patient_data(): void
    {
        $this->switchTenant('clinic-a');

        $url = URL::signedRoute('portal.dashboard', ['tenant' => 'clinic-a', 'patient' => $this->patientA->id]);
        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertSee($this->patientA->name);
    }

    public function test_patient_portal_book_page_loads(): void
    {
        $this->switchTenant('clinic-a');

        $url = URL::signedRoute('portal.book', ['tenant' => 'clinic-a', 'patient' => $this->patientA->id]);
        $response = $this->get($url);

        $response->assertStatus(200);
    }

    public function test_multiple_budgets_can_be_accepted_via_http(): void
    {
        $this->switchTenant('clinic-a');

        $budget1 = $this->createBudgetWithItems($this->patientA, 'pending');
        $budget2 = $this->createBudgetWithItems($this->patientA, 'pending');

        $url1 = URL::signedRoute('portal.budgets.accept', ['tenant' => 'clinic-a', 'budget' => $budget1->id]);
        $url2 = URL::signedRoute('portal.budgets.accept', ['tenant' => 'clinic-a', 'budget' => $budget2->id]);

        $this->post($url1);
        $this->post($url2);

        $this->assertEquals('accepted', $budget1->fresh()->status);
        $this->assertEquals('accepted', $budget2->fresh()->status);
    }

    public function test_patient_portal_isolates_data_between_clinics_via_http(): void
    {
        $this->switchTenant('clinic-a');
        $this->createAppointment($this->patientA, $this->doctorA);
        $this->createBudgetWithItems($this->patientA, 'pending');

        $urlA = URL::signedRoute('portal.dashboard', ['tenant' => 'clinic-a', 'patient' => $this->patientA->id]);
        $responseA = $this->get($urlA);
        $responseA->assertStatus(200);

        $this->switchTenant('clinic-b');
        $this->createAppointment($this->patientB, $this->doctorB);

        $urlB = URL::signedRoute('portal.dashboard', ['tenant' => 'clinic-b', 'patient' => $this->patientB->id]);
        $responseB = $this->get($urlB);
        $responseB->assertStatus(200);
        $responseB->assertViewHas('patient', fn($p) => $p->id === $this->patientB->id);
    }

    public function test_register_success_redirects_without_tenant_id(): void
    {
        $response = $this->get('/register/success');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function test_register_success_redirects_with_invalid_tenant(): void
    {
        $response = $this->get('/register/success?tenant_id=nonexistent');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }
}
