<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Enums\Plan;
use App\Enums\SubscriptionStatus;
use App\Models\User;
use App\Services\PlanLimits;
use App\Services\TenantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

class OnboardingSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRolesAndPermissions();
    }

    public function test_create_tenant_creates_trial_subscription(): void
    {
        $service = app(TenantService::class);

        $clinic = $service->createTenant([
            'subdomain' => 'new-clinic',
            'company_name' => 'New Clinic',
            'name' => 'Admin User',
            'email' => 'admin@newclinic.test',
            'password' => 'password123',
        ]);

        $subscription = $clinic->subscription;

        $this->assertNotNull($subscription);
        $this->assertEquals(SubscriptionStatus::Trialing, $subscription->status);
        $this->assertEquals(Plan::FreeTrial, $subscription->plan);
        $this->assertNotNull($subscription->trial_ends_at);
    }

    public function test_create_tenant_assigns_admin_role(): void
    {
        $service = app(TenantService::class);

        $clinic = $service->createTenant([
            'subdomain' => 'new-clinic',
            'company_name' => 'New Clinic',
            'name' => 'Admin User',
            'email' => 'admin@newclinic.test',
            'password' => 'password123',
        ]);

        $user = User::where('email', 'admin@newclinic.test')->first();

        $this->assertNotNull($user);
        $this->assertEquals('new-clinic', $user->clinic_id);

        Tenancy::initialize('new-clinic');
        setPermissionsTeamId('new-clinic');
        $this->assertTrue($user->fresh()->hasRole('admin'));
    }

    public function test_trial_subscription_has_pro_access(): void
    {
        $service = app(TenantService::class);

        $clinic = $service->createTenant([
            'subdomain' => 'new-clinic',
            'company_name' => 'New Clinic',
            'name' => 'Admin User',
            'email' => 'admin@newclinic.test',
            'password' => 'password123',
        ]);

        $planLimits = app(PlanLimits::class);
        $clinic = $clinic->fresh();

        $this->assertTrue($planLimits->hasAccess($clinic));
        $this->assertEquals(Plan::Pro, $planLimits->effectivePlan($clinic));
    }

    public function test_trial_expires_in_14_days(): void
    {
        $service = app(TenantService::class);

        $clinic = $service->createTenant([
            'subdomain' => 'new-clinic',
            'company_name' => 'New Clinic',
            'name' => 'Admin User',
            'email' => 'admin@newclinic.test',
            'password' => 'password123',
        ]);

        $subscription = $clinic->subscription;

        $this->assertGreaterThan(13, now()->diffInDays($subscription->trial_ends_at));
    }
}
