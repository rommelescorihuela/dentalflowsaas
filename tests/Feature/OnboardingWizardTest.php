<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Plan;
use App\Filament\App\Pages\OnboardingWizard;
use App\Models\Inventory;
use App\Models\ProcedurePrice;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

class OnboardingWizardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRolesAndPermissions();
    }

    public function test_new_tenant_has_onboarding_step_1(): void
    {
        $service = app(TenantService::class);

        $clinic = $service->createTenant([
            'subdomain' => 'onboard-test',
            'company_name' => 'Onboard Test Clinic',
            'name' => 'Admin Test',
            'email' => 'admin@onboard.test',
            'password' => 'password123',
        ]);

        $this->assertEquals(1, $clinic->onboarding_step);
    }

    public function test_plan_prices_are_defined(): void
    {
        $this->assertEquals(39, Plan::Basic->priceUsd());
        $this->assertEquals(89, Plan::Pro->priceUsd());
        $this->assertEquals('Starter', Plan::Basic->label());
        $this->assertEquals('Pro', Plan::Pro->label());
    }

    public function test_dashboard_redirects_to_wizard_when_onboarding_incomplete(): void
    {
        $service = app(TenantService::class);

        $clinic = $service->createTenant([
            'subdomain' => 'onboard-test',
            'company_name' => 'Onboard Test Clinic',
            'name' => 'Admin Test',
            'email' => 'admin@onboard.test',
            'password' => 'password123',
        ]);

        $user = User::where('email', 'admin@onboard.test')->first();

        Tenancy::initialize('onboard-test');
        setPermissionsTeamId('onboard-test');

        $response = $this->actingAs($user)->get('/app');

        $response->assertRedirect('/app/onboarding-wizard');
    }

    public function test_onboarding_step_defaults_to_1(): void
    {
        $this->setUpTenants();

        $this->assertEquals(1, $this->clinicA->onboarding_step);
    }

    public function test_onboarding_imports_procedures(): void
    {
        $service = app(TenantService::class);

        $clinic = $service->createTenant([
            'subdomain' => 'onboard-test',
            'company_name' => 'Onboard Test Clinic',
            'name' => 'Admin Test',
            'email' => 'admin@onboard.test',
            'password' => 'password123',
        ]);

        Tenancy::initialize('onboard-test');

        $this->assertEquals(0, ProcedurePrice::where('clinic_id', 'onboard-test')->count());

        $wizard = new OnboardingWizard;
        $reflection = new \ReflectionClass($wizard);
        $method = $reflection->getMethod('importProcedures');
        $method->setAccessible(true);
        $method->invoke($wizard, 'onboard-test');

        $this->assertGreaterThan(40, ProcedurePrice::where('clinic_id', 'onboard-test')->count());
    }

    public function test_onboarding_imports_inventory(): void
    {
        $service = app(TenantService::class);

        $clinic = $service->createTenant([
            'subdomain' => 'onboard-test',
            'company_name' => 'Onboard Test Clinic',
            'name' => 'Admin Test',
            'email' => 'admin@onboard.test',
            'password' => 'password123',
        ]);

        Tenancy::initialize('onboard-test');

        $this->assertEquals(0, Inventory::where('clinic_id', 'onboard-test')->count());

        $wizard = new OnboardingWizard;
        $reflection = new \ReflectionClass($wizard);
        $method = $reflection->getMethod('importInventory');
        $method->setAccessible(true);
        $method->invoke($wizard, 'onboard-test');

        $this->assertGreaterThan(80, Inventory::where('clinic_id', 'onboard-test')->count());
    }
}
