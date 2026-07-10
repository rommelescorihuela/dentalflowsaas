<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::firstOrCreate(['email' => 'admin@dentalflow.com'], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'clinic_id' => null, // Super admins don't belong to a specific tenant (or belong to multiple)
        ]);

        // Assign super-admin role if it exists (PermissionSeeder should be run first)
        // We defer role assignment to after PermissionSeeder if needed, but assuming PermissionSeeder creates it:

        // Ensure central context before creating permissions and roles
        setPermissionsTeamId(null);

        $this->call([
            PermissionSeeder::class,
            TenantSeeder::class,
            SubscriptionSeeder::class,
        ]);

        setPermissionsTeamId(null);
        // Reload from DB to ensure fresh Spatie context after PermissionSeeder ran
        $superAdmin = User::where('email', 'admin@dentalflow.com')->first();
        if ($superAdmin && ! $superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole('super-admin');
        }
    }
}
