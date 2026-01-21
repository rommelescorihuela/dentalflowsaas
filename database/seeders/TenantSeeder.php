<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Create Tenant 1
        $clinic1 = Clinic::firstOrCreate(['id' => 'clinic1'], [
            'name' => 'Clínica Dental Sonrisas',
            'data' => ['plan' => 'enterprise'], // Example data
        ]);

        $clinic1->domains()->firstOrCreate(['domain' => 'clinic1.localhost']);

        // Create User for Tenant 1
        setPermissionsTeamId($clinic1->id);
        User::firstOrCreate(['email' => 'house@clinic1.com'], [
            'name' => 'Dr. House',
            'password' => 'password',
            'tenant_id' => $clinic1->id,
        ])->assignRole('admin');

        // Seed data for Tenant 1
        \App\Models\Patient::factory()->count(15)->create(['tenant_id' => $clinic1->id]);
        \App\Models\Inventory::factory()->count(20)->create(['tenant_id' => $clinic1->id]);
        \App\Models\ProcedurePrice::factory()->count(10)->create(['tenant_id' => $clinic1->id]);

        // Create Tenant 2
        $clinic2 = Clinic::firstOrCreate(['id' => 'clinic2'], [
            'name' => 'Ortodoncia Pérez',
        ]);

        $clinic2->domains()->firstOrCreate(['domain' => 'clinic2.localhost']);

        setPermissionsTeamId($clinic2->id);
        User::firstOrCreate(['email' => 'strange@clinic2.com'], [
            'name' => 'Dr. Strange',
            'password' => Hash::make('password'),
            'tenant_id' => $clinic2->id,
        ])->assignRole('admin');

        // Seed data for Tenant 2
        \App\Models\Patient::factory()->count(10)->create(['tenant_id' => $clinic2->id]);
        \App\Models\Inventory::factory()->count(15)->create(['tenant_id' => $clinic2->id]);
        \App\Models\ProcedurePrice::factory()->count(8)->create(['tenant_id' => $clinic2->id]);
    }
}
