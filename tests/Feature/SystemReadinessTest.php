<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Clinic;
use App\Models\Patient;

class SystemReadinessTest extends TestCase
{
    /**
     * Test that the landing page loads successfully.
     */
    public function test_landing_page_loads_successfully()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test that the register page loads successfully via Livewire/Blade.
     */
    public function test_register_page_loads_successfully()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * Test that the portal base route is correctly protected.
     */
    public function test_portal_dashboard_route_exists()
    {
        $response = $this->get('/demo-tenant/portal/1');
        
        // Should require signature or authentication, not 404 or 500
        $response->assertStatus(403);
    }
}
