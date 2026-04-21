<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Patient;
use App\Models\Odontogram;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_app_boots_successfully(): void
    {
        $this->assertTrue(class_exists(Patient::class));
        $this->assertTrue(class_exists(Odontogram::class));
    }

    public function test_app_config_is_loaded(): void
    {
        $this->assertEquals('pgsql', config('database.default'));
        $this->assertNotNull(config('app.key'));
    }

    public function test_filament_is_configured(): void
    {
        $this->assertTrue(class_exists('Filament\Panel'));
    }

    public function test_tenancy_is_configured(): void
    {
        $this->assertNotNull(config('tenancy.tenant_model'));
    }
}