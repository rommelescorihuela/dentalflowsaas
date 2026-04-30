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

        // Register domains for local and production
        $centralDomain = env('TENANCY_CENTRAL_DOMAINS', 'localhost');
        $centralDomain = trim(explode(',', $centralDomain)[0]);

        $clinic1->domains()->firstOrCreate(['domain' => 'clinic1.localhost']);
        $clinic1->domains()->firstOrCreate(['domain' => "clinic1.{$centralDomain}"]);

        // Initialize tenancy for clinic1
        tenancy()->initialize($clinic1);

        // Create User for Tenant 1
        setPermissionsTeamId($clinic1->id);
        User::firstOrCreate(['email' => 'house@clinic1.com'], [
            'name' => 'Dr. House',
            'password' => 'password',
            'clinic_id' => $clinic1->id,
        ])->assignRole('admin');

        // Seed data for Tenant 1
        $this->call(ProcedurePriceSeeder::class);
        $this->call(InventorySeeder::class);
        $procedures = \App\Models\ProcedurePrice::where('clinic_id', $clinic1->id)->get();
        $patients = \App\Models\Patient::factory()->count(20)->create(['clinic_id' => $clinic1->id]);

        // Create demo odontograms with clinical records for first 5 patients
        $demoPatients = $patients->take(5);
        foreach ($demoPatients as $patient) {
            $odontogram = \App\Models\Odontogram::create([
                'clinic_id' => $clinic1->id,
                'patient_id' => $patient->id,
                'name' => 'Odontograma Inicial',
                'date' => now()->subDays(rand(1, 30)),
                'status' => 'in_progress',
            ]);

            // Add some demo clinical records
            $demoRecords = [
                ['tooth' => 16, 'surface' => 'center', 'diagnosis' => 'caries', 'status' => 'planned'],
                ['tooth' => 16, 'surface' => 'top', 'diagnosis' => 'caries', 'status' => 'planned'],
                ['tooth' => 24, 'surface' => 'center', 'diagnosis' => 'filled', 'status' => 'completed'],
                ['tooth' => 36, 'surface' => 'center', 'diagnosis' => 'endodontic', 'status' => 'completed'],
                ['tooth' => 36, 'surface' => 'root', 'diagnosis' => 'endodontic', 'status' => 'completed'],
                ['tooth' => 46, 'surface' => 'center', 'diagnosis' => 'crown', 'status' => 'completed'],
                ['tooth' => 11, 'surface' => 'center', 'diagnosis' => 'missing', 'status' => 'completed'],
                ['tooth' => 21, 'surface' => 'center', 'diagnosis' => 'filled', 'status' => 'existing'],
            ];

            foreach ($demoRecords as $rec) {
                \App\Models\ClinicalRecord::create([
                    'clinic_id' => $clinic1->id,
                    'patient_id' => $patient->id,
                    'odontogram_id' => $odontogram->id,
                    'tooth_number' => $rec['tooth'],
                    'surface' => $rec['surface'],
                    'diagnosis_code' => $rec['diagnosis'],
                    'treatment_status' => $rec['status'],
                    'notes' => 'Registro demo',
                ]);
            }
        }

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
        $clinic2->domains()->firstOrCreate(['domain' => "clinic2.{$centralDomain}"]);

        // Initialize tenancy for clinic2
        tenancy()->initialize($clinic2);

        setPermissionsTeamId($clinic2->id);
        User::firstOrCreate(['email' => 'strange@clinic2.com'], [
            'name' => 'Dr. Strange',
            'password' => Hash::make('password'),
            'clinic_id' => $clinic2->id,
        ])->assignRole('admin');

        // Seed data for Tenant 2
        $this->call(ProcedurePriceSeeder::class);
        $this->call(InventorySeeder::class);
        \App\Models\Patient::factory()->count(5)->create(['clinic_id' => $clinic2->id]);
    }
}
