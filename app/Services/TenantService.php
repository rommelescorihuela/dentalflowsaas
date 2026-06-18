<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Plan;
use App\Enums\SubscriptionStatus;
use App\Models\Clinic;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Stancl\Tenancy\Facades\Tenancy;

class TenantService
{
    /**
     * Create a new Tenant (Clinic), Domain, and Admin User.
     */
    public function createTenant(array $data): Clinic
    {
        return DB::transaction(function () use ($data) {
            $tenantId = $data['subdomain'];

            if (Clinic::find($tenantId)) {
                throw ValidationException::withMessages([
                    'subdomain' => 'This subdomain is already taken.',
                ]);
            }

            $clinic = Clinic::create([
                'id' => $tenantId,
                'name' => $data['company_name'],
                'plan' => 'free_trial',
                'subscription_status' => SubscriptionStatus::Trialing->value,
                'trial_ends_at' => now()->addDays(14),
            ]);

            Subscription::create([
                'clinic_id' => $clinic->id,
                'plan' => Plan::FreeTrial->value,
                'status' => SubscriptionStatus::Trialing->value,
                'trial_ends_at' => now()->addDays(14),
            ]);

            $centralDomain = config('tenancy.central_domains')[0] ?? 'localhost';
            $isLocal = in_array($centralDomain, ['localhost', '127.0.0.1', '::1']);

            if (! $isLocal) {
                $clinic->domains()->firstOrCreate([
                    'domain' => $data['subdomain'].'.'.$centralDomain,
                ]);
            }

            Tenancy::initialize($clinic);
            setPermissionsTeamId($clinic->id);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'clinic_id' => $clinic->id,
            ]);

            $user->assignRole('admin');

            Tenancy::end();
            setPermissionsTeamId(null);

            return $clinic;
        });
    }
}
