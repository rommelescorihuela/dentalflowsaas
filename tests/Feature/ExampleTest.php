<?php

namespace Tests\Feature;

use App\Models\Odontogram;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
        $this->assertStringStartsWith('base64:', config('app.key'));
    }

    public function test_filament_is_configured(): void
    {
        $this->assertTrue(class_exists('Filament\Panel'));
    }
}
