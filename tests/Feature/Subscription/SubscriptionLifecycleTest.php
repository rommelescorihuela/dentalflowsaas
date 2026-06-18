<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Enums\Plan;
use App\Enums\SubscriptionStatus;
use App\Models\Clinic;
use App\Models\SubscriptionPayment;
use App\Services\PlanLimits;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_tenant_service_creates_trial_subscription(): void
    {
        $service = app(SubscriptionService::class);
        $clinic = Clinic::create(['id' => 'test-clinic', 'name' => 'Test Clinic']);

        $subscription = $service->createTrial($clinic);

        $this->assertEquals(SubscriptionStatus::Trialing, $subscription->status);
        $this->assertEquals(Plan::FreeTrial, $subscription->plan);
        $this->assertNotNull($subscription->trial_ends_at);
        $this->assertGreaterThan(13, now()->diffInDays($subscription->trial_ends_at));
    }

    public function test_trialing_subscription_has_pro_plan_access(): void
    {
        $service = app(SubscriptionService::class);
        $clinic = Clinic::create(['id' => 'test-clinic', 'name' => 'Test Clinic']);
        $service->createTrial($clinic);

        $planLimits = app(PlanLimits::class);

        $this->assertTrue($planLimits->hasAccess($clinic));
        $this->assertEquals(Plan::Pro, $planLimits->effectivePlan($clinic));
    }

    public function test_activate_subscription_from_trial(): void
    {
        $service = app(SubscriptionService::class);
        $clinic = Clinic::create(['id' => 'test-clinic', 'name' => 'Test Clinic']);
        $service->createTrial($clinic);

        $payment = SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'amount' => 99,
            'currency' => 'USD',
            'method' => 'zelle_usd',
            'status' => 'pending',
            'reference' => 'TEST-001',
            'paid_at' => now(),
        ]);

        $subscription = $service->activate($clinic, Plan::Basic, $payment);

        $this->assertEquals(SubscriptionStatus::Active, $subscription->status);
        $this->assertEquals(Plan::Basic, $subscription->plan);
        $this->assertNotNull($subscription->current_period_end);
        $this->assertEquals('approved', $payment->fresh()->status);
    }

    public function test_extend_active_subscription(): void
    {
        $service = app(SubscriptionService::class);
        $clinic = Clinic::create(['id' => 'test-clinic', 'name' => 'Test Clinic']);
        $subscription = $service->createTrial($clinic);

        $payment1 = SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'amount' => 99,
            'currency' => 'USD',
            'method' => 'zelle_usd',
            'status' => 'pending',
            'paid_at' => now(),
        ]);
        $service->activate($clinic, Plan::Pro, $payment1);

        $originalEnd = $subscription->fresh()->current_period_end;

        $payment2 = SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'amount' => 99,
            'currency' => 'USD',
            'method' => 'zelle_usd',
            'status' => 'pending',
            'paid_at' => now(),
        ]);

        $service->extend($clinic, $payment2);

        $this->assertEquals(SubscriptionStatus::Active, $subscription->fresh()->status);
        $this->assertTrue($subscription->fresh()->current_period_end > $originalEnd);
    }

    public function test_past_due_then_suspend(): void
    {
        $service = app(SubscriptionService::class);
        $clinic = Clinic::create(['id' => 'test-clinic', 'name' => 'Test Clinic']);
        $service->createTrial($clinic);

        $service->markPastDue($clinic);
        $this->assertEquals(SubscriptionStatus::PastDue, $clinic->fresh()->subscription->status);
        $this->assertTrue(app(PlanLimits::class)->hasAccess($clinic->fresh()));

        $service->suspend($clinic);
        $this->assertEquals(SubscriptionStatus::Suspended, $clinic->fresh()->subscription->status);
        $this->assertFalse(app(PlanLimits::class)->hasAccess($clinic->fresh()));
    }

    public function test_cancel_subscription(): void
    {
        $service = app(SubscriptionService::class);
        $clinic = Clinic::create(['id' => 'test-clinic', 'name' => 'Test Clinic']);
        $service->createTrial($clinic);

        $service->cancel($clinic);

        $this->assertEquals(SubscriptionStatus::Cancelled, $clinic->fresh()->subscription->status);
        $this->assertNotNull($clinic->fresh()->subscription->cancelled_at);
    }

    public function test_change_plan(): void
    {
        $service = app(SubscriptionService::class);
        $clinic = Clinic::create(['id' => 'test-clinic', 'name' => 'Test Clinic']);
        $service->createTrial($clinic);

        $service->changePlan($clinic, Plan::Enterprise);

        $this->assertEquals(Plan::Enterprise, $clinic->fresh()->subscription->plan);
    }
}
