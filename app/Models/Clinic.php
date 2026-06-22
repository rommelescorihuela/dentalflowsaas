<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ActivityLogger;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Clinic extends BaseTenant
{
    use ActivityLogger, HasDomains;

    public function domains(): HasMany
    {
        return $this->hasMany(config('tenancy.domain_model'), 'clinic_id');
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getOnboardingStepAttribute()
    {
        $data = $this->data;

        if (! is_array($data) || empty($data)) {
            $rawData = \Illuminate\Support\Facades\DB::table('tenants')
                ->where('id', $this->id)
                ->value('data');

            $data = $rawData ? json_decode($rawData, true) : [];
        }

        return $data['onboarding_step'] ?? 1;
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
