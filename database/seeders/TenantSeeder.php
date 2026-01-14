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
        $clinic1 = Clinic::create([
            'id' => 'clinic1',
            'name' => 'Clínica Dental Sonrisas',
            'data' => ['plan' => 'enterprise'], // Example data
        ]);

        $clinic1->domains()->create(['domain' => 'clinic1.localhost']);

        // Create User for Tenant 1
        User::create([
            'name' => 'Dr. House',
            'email' => 'house@clinic1.com',
            'password' => Hash::make('password'),
            'tenant_id' => $clinic1->id,
        ]);

        // Create Tenant 2
        $clinic2 = Clinic::create([
            'id' => 'clinic2',
            'name' => 'Ortodoncia Pérez',
        ]);

        $clinic2->domains()->create(['domain' => 'clinic2.localhost']);

        User::create([
            'name' => 'Dr. Strange',
            'email' => 'strange@clinic2.com',
            'password' => Hash::make('password'),
            'tenant_id' => $clinic2->id,
        ]);
    }
}
