<?php

declare(strict_types=1);

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use App\Traits\ActivityLogger;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinic extends BaseTenant
{
    use HasDomains, ActivityLogger;

    public function domains(): HasMany
    {
        return $this->hasMany(config('tenancy.domain_model'), 'clinic_id');
    }

    public function getOnboardingStepAttribute()
    {
        return $this->data['onboarding_step'] ?? 1;
    }

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'plan',
            'data',
        ];
    }

    // VirtualColumn magic enabled by default in BaseTenant
}
