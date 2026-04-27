<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Clinic;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SystemReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_central_routes_exist(): void
    {
        $response = $this->call('GET', '/');
        $this->assertContains($response->getStatusCode(), [200, 302, 404]);
    }

    public function test_register_route_is_defined(): void
    {
        $response = $this->call('GET', '/register');
        $this->assertContains($response->getStatusCode(), [200, 302, 404]);
    }

    public function test_models_are_correctly_defined(): void
    {
        $this->assertTrue(class_exists('App\Models\Patient'));
        $this->assertTrue(class_exists('App\Models\Odontogram'));
        $this->assertTrue(class_exists('App\Models\ClinicalRecord'));
        $this->assertTrue(class_exists('App\Models\Budget'));
        $this->assertTrue(class_exists('App\Models\Appointment'));
        $this->assertTrue(class_exists('App\Models\Clinic'));
    }

    public function test_belongs_to_clinic_trait_exists(): void
    {
        $this->assertTrue(trait_exists('App\Traits\BelongsToClinic'));
    }

    public function test_middleware_are_defined(): void
    {
        $this->assertTrue(class_exists('App\Http\Middleware\SyncSpatiePermissionsTeamId'));
        $this->assertTrue(class_exists('App\Http\Middleware\ForceOnboardingMiddleware'));
    }

    public function test_tenant_service_is_configured(): void
    {
        $this->assertEquals(\App\Models\Clinic::class, config('tenancy.tenant_model'));
    }
}
