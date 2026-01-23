<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDomains;

use App\Traits\ActivityLogger;

class Clinic extends BaseTenant
{
    use HasDomains, ActivityLogger;

    protected $casts = [
        'data' => 'array',
    ];

    public function domains()
    {
        return $this->hasMany(config('tenancy.domain_model'), 'clinic_id');
    }

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'plan',
            'data',
            'onboarding_step',
        ];
    }

    // Disable VirtualColumn magic to use standard Eloquent JSON column
    protected function encodeAttributes(): void
    {
    }
    protected function decodeAttributes()
    {
    }
    protected function decodeVirtualColumn(): void
    {
    }
}
