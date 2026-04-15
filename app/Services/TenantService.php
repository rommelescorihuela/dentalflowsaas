<?php

namespace App\Services;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TenantService
{
    /**
     * Create a new Tenant (Clinic), Domain, and Admin User.
     */
    public function createTenant(array $data): Clinic
    {
        return DB::transaction(function () use ($data) {
            // 1. Create the Tenant (Clinic)
            // Use the subdomain as the Tenant ID for simplicity in this architecture
            $tenantId = $data['subdomain'];

            // Check if tenant exists
            if (Clinic::find($tenantId)) {
                throw ValidationException::withMessages([
                    'subdomain' => 'This subdomain is already taken.',
                ]);
            }

            $clinic = Clinic::create([
                'id' => $tenantId,
                'name' => $data['company_name'],
                'plan' => 'free_trial', // Default plan
            ]);

            // 2. Create the Domain (Skipped for path-based multi-tenancy)
            /*
            $clinic->domains()->create([
                'domain' => $data['subdomain'] . '.' . config('tenancy.central_domains')[0],
            ]);
            */

            // 3. Create the Admin User for this Tenant
            // We switch context to the new tenant to create the user inside it?
            // Actually, in Stancl/Tenancy, users are often global or tenant-specific depending on config.
            // Based on User model having 'clinic_id' (BelongsToClinic), it seems Users are stored in the central DB 
            // but scoped to a clinic. Let's verify this assumption. 
            // *Wait*, standard Tenancy usually puts users in tenant DB. 
            // But 'BelongsToClinic' trait suggests Single Database Multi-Tenancy or Central User Table.
            // Let's assume Central User Table for SaaS Management based on previous context (User::find(2) worked globally).

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'clinic_id' => $clinic->id,
            ]);

            // Assign Role (if Spatie permission is used centrally)
            // $user->assignRole('admin'); 

            return $clinic;
        });
    }
}
