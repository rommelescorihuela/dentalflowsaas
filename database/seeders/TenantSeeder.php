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
            'clinic_id' => $clinic1->id,
        ])->assignRole('admin');

        // Seed data for Tenant 1
        $procedures = \App\Models\ProcedurePrice::factory()->count(10)->create(['clinic_id' => $clinic1->id]);
        $patients = \App\Models\Patient::factory()->count(20)->create(['clinic_id' => $clinic1->id]);
        \App\Models\Inventory::factory()->count(20)->create(['clinic_id' => $clinic1->id]);

        foreach ($patients as $patient) {
            // 1. Appointments & Treatments
            for ($i = 0; $i < rand(1, 3); $i++) {
                $date = now()->subDays(rand(0, 60))->addDays(rand(0, 30));
                $procedure = $procedures->random();

                $appointment = \App\Models\Appointment::create([
                    'clinic_id' => $clinic1->id,
                    'patient_id' => $patient->id,
                    // 'user_id' => $user->id, 
                    'procedure_price_id' => $procedure->id,
                    'start_time' => $date->setTime(rand(9, 17), 0),
                    'end_time' => $date->copy()->setTime(rand(9, 17), 30),
                    'status' => $date->isPast() ? 'completed' : 'scheduled',
                ]);

                // If completed, create a Treatment record linked to this appointment
                if ($appointment->status === 'completed') {
                    \App\Models\Treatment::create([
                        'clinic_id' => $clinic1->id,
                        'appointment_id' => $appointment->id, // New relationship
                        'name' => $procedure->procedure_name,
                        'price' => $procedure->price,
                        'code' => $procedure->code,
                        'created_at' => $appointment->end_time,
                    ]);
                }
            }

            // 2. Budgets & Payments (Financial Data)
            if (rand(0, 1)) {
                $budgetAmount = rand(100, 1000);
                $accepted = rand(0, 1);
                $budget = \App\Models\Budget::create([
                    'clinic_id' => $clinic1->id,
                    'patient_id' => $patient->id,
                    'total' => $budgetAmount,
                    'status' => $accepted ? 'accepted' : 'pending',
                ]);

                if ($accepted) {
                    // Create a payment for a portion or full amount, LINKED to the budget
                    \App\Models\Payment::create([
                        'clinic_id' => $clinic1->id,
                        'budget_id' => $budget->id, // New relationship
                        'patient_id' => $patient->id,
                        'amount' => rand(50, $budgetAmount),
                        'method' => ['cash', 'card', 'transfer'][rand(0, 2)],
                        'paid_at' => now()->subMonths(rand(0, 11)),
                    ]);
                }
            }
        }

        // Create Tenant 2
        $clinic2 = Clinic::firstOrCreate(['id' => 'clinic2'], [
            'name' => 'Ortodoncia Pérez',
        ]);

        $clinic2->domains()->firstOrCreate(['domain' => 'clinic2.localhost']);

        setPermissionsTeamId($clinic2->id);
        User::firstOrCreate(['email' => 'strange@clinic2.com'], [
            'name' => 'Dr. Strange',
            'password' => Hash::make('password'),
            'clinic_id' => $clinic2->id,
        ])->assignRole('admin');

        // Seed data for Tenant 2 (Simpler)
        \App\Models\Patient::factory()->count(5)->create(['clinic_id' => $clinic2->id]);
    }
}
