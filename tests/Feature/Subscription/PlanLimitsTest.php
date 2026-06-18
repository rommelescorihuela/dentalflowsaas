<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Enums\Plan;
use App\Models\Clinic;
use App\Models\SubscriptionPayment;
use App\Services\PlanLimits;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

class PlanLimitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_trial_plan_has_pro_features(): void
    {
        $clinic = Clinic::create(['id' => 'limit-test', 'name' => 'Limit Test']);
        app(SubscriptionService::class)->createTrial($clinic);

        $planLimits = app(PlanLimits::class);

        $this->assertTrue($planLimits->hasFeature($clinic, 'odontogram'));
        $this->assertTrue($planLimits->hasFeature($clinic, 'bi_reports'));
    }

    public function test_patient_limit_enforced_for_basic_plan(): void
    {
        $clinic = Clinic::create(['id' => 'limit-test', 'name' => 'Limit Test']);
        $service = app(SubscriptionService::class);
        $service->createTrial($clinic);

        Tenancy::initialize($clinic);
        setPermissionsTeamId($clinic->id);

        $payment = SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'amount' => 49,
            'currency' => 'USD',
            'method' => 'zelle_usd',
            'status' => 'pending',
            'paid_at' => now(),
        ]);
        $service->activate($clinic, Plan::Basic, $payment);

        Tenancy::end();
        setPermissionsTeamId(null);

        $planLimits = app(PlanLimits::class);
        $this->assertEquals(500, $planLimits->effectivePlan($clinic)->patientsLimit());
    }

    public function test_pro_plan_has_unlimited_patients(): void
    {
        $clinic = Clinic::create(['id' => 'limit-test', 'name' => 'Limit Test']);
        $planLimits = app(PlanLimits::class);

        $service = app(SubscriptionService::class);
        $service->createTrial($clinic);

        $this->assertNull($planLimits->effectivePlan($clinic)->patientsLimit());
    }

    public function test_suspended_clinic_has_no_access(): void
    {
        $clinic = Clinic::create(['id' => 'limit-test', 'name' => 'Limit Test']);
        $service = app(SubscriptionService::class);
        $service->createTrial($clinic);
        $service->suspend($clinic);

        $planLimits = app(PlanLimits::class);

        $this->assertFalse($planLimits->hasAccess($clinic->fresh()));
    }
}
