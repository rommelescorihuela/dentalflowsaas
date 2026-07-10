<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Clinic;
use App\Models\Domain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_clinic_has_domains_relationship(): void
    {
        $clinic = Clinic::create([
            'id' => 'clinic-with-domain',
            'name' => 'Clinic With Domain',
        ]);

        $this->assertGreaterThanOrEqual(1, $clinic->domains->count());
    }

    public function test_clinic_deletion_removes_domains(): void
    {
        $clinic = Clinic::create([
            'id' => 'to-delete',
            'name' => 'To Delete',
        ]);

        $initialDomainCount = $clinic->domains->count();
        $this->assertGreaterThanOrEqual(1, $initialDomainCount);

        $clinic->delete();

        $this->assertEquals(0, Domain::where('clinic_id', $clinic->id)->count());
    }
}
