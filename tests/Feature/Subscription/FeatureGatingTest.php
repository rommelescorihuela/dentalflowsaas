<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Enums\Plan;
use App\Models\Budget;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\SubscriptionPayment;
use App\Services\PlanLimits;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

class FeatureGatingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_pro_plan_has_all_features(): void
    {
        $planLimits = app(PlanLimits::class);
        $clinic = $this->clinicA->fresh();

        $this->assertTrue($planLimits->hasFeature($clinic, 'portal'));
        $this->assertTrue($planLimits->hasFeature($clinic, 'pdf'));
        $this->assertTrue($planLimits->hasFeature($clinic, 'bi_reports'));
        $this->assertTrue($planLimits->hasFeature($clinic, 'low_inventory_alert'));
    }

    public function test_starter_plan_does_not_have_portal_feature(): void
    {
        $service = app(SubscriptionService::class);

        // Crear clínica con plan Starter
        $clinic = Clinic::create(['id' => 'starter-clinic', 'name' => 'Starter Clinic']);
        $service->createTrial($clinic);

        $payment = SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'amount' => 39,
            'currency' => 'USD',
            'method' => 'zelle_usd',
            'status' => 'pending',
            'paid_at' => now(),
        ]);
        $service->activate($clinic, Plan::Basic, $payment);

        $planLimits = app(PlanLimits::class);
        $clinic = $clinic->fresh();

        $this->assertFalse($planLimits->hasFeature($clinic, 'portal'));
        $this->assertTrue($planLimits->hasFeature($clinic, 'pdf'));
        $this->assertFalse($planLimits->hasFeature($clinic, 'bi_reports'));
        $this->assertFalse($planLimits->hasFeature($clinic, 'low_inventory_alert'));
        $this->assertTrue($planLimits->hasFeature($clinic, 'odontogram'));
        $this->assertTrue($planLimits->hasFeature($clinic, 'budgets'));
    }

    public function test_starter_clinic_portal_returns_403(): void
    {
        $service = app(SubscriptionService::class);

        $clinic = Clinic::create(['id' => 'starter-clinic', 'name' => 'Starter Clinic']);
        $service->createTrial($clinic);

        $payment = SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'amount' => 39,
            'currency' => 'USD',
            'method' => 'zelle_usd',
            'status' => 'pending',
            'paid_at' => now(),
        ]);
        $service->activate($clinic, Plan::Basic, $payment);

        Tenancy::initialize('starter-clinic');
        setPermissionsTeamId('starter-clinic');

        $patient = Patient::create([
            'name' => 'Test Patient',
            'email' => 'test@starter.test',
            'phone' => '+56911111111',
            'clinic_id' => 'starter-clinic',
        ]);

        $url = URL::signedRoute('portal.dashboard', [
            'tenant' => 'starter-clinic',
            'patient' => $patient->id,
        ]);

        $response = $this->get($url);

        $response->assertStatus(403);
    }

    public function test_starter_clinic_pdf_returns_200(): void
    {
        $service = app(SubscriptionService::class);

        $clinic = Clinic::create(['id' => 'starter-clinic', 'name' => 'Starter Clinic']);
        $service->createTrial($clinic);

        $payment = SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'amount' => 39,
            'currency' => 'USD',
            'method' => 'zelle_usd',
            'status' => 'pending',
            'paid_at' => now(),
        ]);
        $service->activate($clinic, Plan::Basic, $payment);

        Tenancy::initialize('starter-clinic');

        $patient = Patient::create([
            'name' => 'Test Patient',
            'email' => 'test@starter.test',
            'phone' => '+56911111111',
            'clinic_id' => 'starter-clinic',
        ]);

        $budget = Budget::create([
            'clinic_id' => 'starter-clinic',
            'patient_id' => $patient->id,
            'total' => 50000,
            'status' => 'sent',
        ]);

        $response = $this->get("/app/budgets/{$budget->id}/pdf");

        $response->assertStatus(200);
    }

    public function test_trial_clinic_has_all_features(): void
    {
        $service = app(SubscriptionService::class);

        $clinic = Clinic::create(['id' => 'trial-clinic', 'name' => 'Trial Clinic']);
        $service->createTrial($clinic);

        $planLimits = app(PlanLimits::class);
        $clinic = $clinic->fresh();

        $this->assertTrue($planLimits->hasFeature($clinic, 'portal'));
        $this->assertTrue($planLimits->hasFeature($clinic, 'pdf'));
        $this->assertTrue($planLimits->hasFeature($clinic, 'bi_reports'));
    }
}
