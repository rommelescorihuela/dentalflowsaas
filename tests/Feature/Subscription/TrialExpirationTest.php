<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Enums\SubscriptionStatus;
use App\Models\Clinic;
use App\Services\PlanLimits;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrialExpirationTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_trial_moves_to_past_due(): void
    {
        $clinic = Clinic::create(['id' => 'trial-exp', 'name' => 'Trial Exp']);
        $service = app(SubscriptionService::class);

        $subscription = $service->createTrial($clinic);

        $subscription->update(['trial_ends_at' => now()->subDay()]);

        $service->markPastDue($clinic);

        $this->assertEquals(SubscriptionStatus::PastDue, $clinic->fresh()->subscription->status);
    }

    public function test_past_due_still_has_access(): void
    {
        $clinic = Clinic::create(['id' => 'trial-exp', 'name' => 'Trial Exp']);
        $service = app(SubscriptionService::class);
        $service->createTrial($clinic);

        $service->markPastDue($clinic);

        $planLimits = app(PlanLimits::class);
        $this->assertTrue($planLimits->hasAccess($clinic->fresh()));
    }

    public function test_past_due_after_grace_period_suspends(): void
    {
        $clinic = Clinic::create(['id' => 'trial-exp', 'name' => 'Trial Exp']);
        $service = app(SubscriptionService::class);
        $subscription = $service->createTrial($clinic);

        $subscription->update(['trial_ends_at' => now()->subDays(8)]);

        $service->markPastDue($clinic);
        $service->suspend($clinic);

        $this->assertEquals(SubscriptionStatus::Suspended, $clinic->fresh()->subscription->status);
        $this->assertFalse(app(PlanLimits::class)->hasAccess($clinic->fresh()));
    }

    public function test_active_period_end_moves_to_past_due(): void
    {
        $clinic = Clinic::create(['id' => 'trial-exp', 'name' => 'Trial Exp']);
        $service = app(SubscriptionService::class);
        $service->createTrial($clinic);

        $subscription = $clinic->subscription;
        $subscription->update([
            'status' => SubscriptionStatus::Active->value,
            'current_period_end' => now()->subDay(),
        ]);

        $service->markPastDue($clinic);

        $this->assertEquals(SubscriptionStatus::PastDue, $clinic->fresh()->subscription->status);
    }

    public function test_process_subscriptions_command_expires_trial(): void
    {
        $clinic = Clinic::create(['id' => 'trial-exp', 'name' => 'Trial Exp']);
        $service = app(SubscriptionService::class);
        $subscription = $service->createTrial($clinic);

        $subscription->update(['trial_ends_at' => now()->subDay()]);

        $this->artisan('subscriptions:process')
            ->assertSuccessful()
            ->expectsOutputToContain('Trials expirados');

        $this->assertEquals(SubscriptionStatus::PastDue, $clinic->fresh()->subscription->status);
    }
}
