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
        $centralDomain = env('TENANCY_CENTRAL_DOMAINS', 'localhost');
        $centralDomain = trim(explode(',', $centralDomain)[0]);

        // ============================================================
        // CLINIC 1 — Clínica Dental Sonrisas
        // ============================================================
        $clinic1 = Clinic::firstOrCreate(['id' => 'clinic1'], [
            'name' => 'Clínica Dental Sonrisas',
            'data' => ['plan' => 'enterprise'],
        ]);

        $clinic1->domains()->firstOrCreate(['domain' => 'clinic1.localhost']);
        $clinic1->domains()->firstOrCreate(['domain' => "clinic1.{$centralDomain}"]);

        tenancy()->initialize($clinic1);
        setPermissionsTeamId($clinic1->id);

        // Users for clinic1
        $admin1 = User::firstOrCreate(['email' => 'admin@clinic1.com'], [
            'name' => 'Admin Sonrisas',
            'password' => Hash::make('password'),
            'clinic_id' => $clinic1->id,
        ]);
        $admin1->assignRole('admin');

        $doctor1 = User::firstOrCreate(['email' => 'house@clinic1.com'], [
            'name' => 'Dr. House',
            'password' => Hash::make('password'),
            'clinic_id' => $clinic1->id,
        ]);
        $doctor1->assignRole('doctor');

        $assistant1 = User::firstOrCreate(['email' => 'assistant@clinic1.com'], [
            'name' => 'Asistente María',
            'password' => Hash::make('password'),
            'clinic_id' => $clinic1->id,
        ]);
        $assistant1->assignRole('assistant');

        // Seed master data
        $this->call(ProcedurePriceSeeder::class);
        $this->call(InventorySeeder::class);
        $this->call(ProcedureInventorySeeder::class);

        $procedures = \App\Models\ProcedurePrice::where('clinic_id', $clinic1->id)->get();
        $patients = \App\Models\Patient::factory()->count(20)->create([
            'clinic_id' => $clinic1->id,
            'doctor_id' => $doctor1->id,
        ]);

        // Demo odontograms for first 5 patients
        $this->seedOdontograms($clinic1, $patients->take(5), $procedures);

        // Appointments, budgets and payments for all patients
        $this->seedAppointmentsAndBudgets($clinic1, $patients, $doctor1, $procedures);

        // ============================================================
        // CLINIC 2 — Ortodoncia Pérez
        // ============================================================
        $clinic2 = Clinic::firstOrCreate(['id' => 'clinic2'], [
            'name' => 'Ortodoncia Pérez',
            'data' => ['plan' => 'professional'],
        ]);

        $clinic2->domains()->firstOrCreate(['domain' => 'clinic2.localhost']);
        $clinic2->domains()->firstOrCreate(['domain' => "clinic2.{$centralDomain}"]);

        tenancy()->initialize($clinic2);
        setPermissionsTeamId($clinic2->id);

        // Users for clinic2
        $admin2 = User::firstOrCreate(['email' => 'admin@clinic2.com'], [
            'name' => 'Admin Pérez',
            'password' => Hash::make('password'),
            'clinic_id' => $clinic2->id,
        ]);
        $admin2->assignRole('admin');

        $doctor2 = User::firstOrCreate(['email' => 'strange@clinic2.com'], [
            'name' => 'Dr. Strange',
            'password' => Hash::make('password'),
            'clinic_id' => $clinic2->id,
        ]);
        $doctor2->assignRole('doctor');

        $assistant2 = User::firstOrCreate(['email' => 'assistant@clinic2.com'], [
            'name' => 'Asistente Ana',
            'password' => Hash::make('password'),
            'clinic_id' => $clinic2->id,
        ]);
        $assistant2->assignRole('assistant');

        // Seed master data for clinic2
        $this->call(ProcedurePriceSeeder::class);
        $this->call(InventorySeeder::class);
        $this->call(ProcedureInventorySeeder::class);

        $proceduresC2 = \App\Models\ProcedurePrice::where('clinic_id', $clinic2->id)->get();
        $patientsC2 = \App\Models\Patient::factory()->count(10)->create([
            'clinic_id' => $clinic2->id,
            'doctor_id' => $doctor2->id,
        ]);

        // Demo odontograms for first 3 patients in clinic2
        $this->seedOdontograms($clinic2, $patientsC2->take(3), $proceduresC2);

        // Appointments, budgets and payments for clinic2
        $this->seedAppointmentsAndBudgets($clinic2, $patientsC2, $doctor2, $proceduresC2);

        // Reset tenancy and team context to avoid leaking state to other seeders
        tenancy()->end();
        setPermissionsTeamId(null);
    }

    /**
     * Create demo odontograms with clinical records for the given patients.
     */
    private function seedOdontograms(Clinic $clinic, $patients, $procedures): void
    {
        $demoRecords = [
            ['tooth' => 16, 'surface' => 'center', 'diagnosis' => 'caries',    'status' => 'planned'],
            ['tooth' => 16, 'surface' => 'top',    'diagnosis' => 'caries',    'status' => 'planned'],
            ['tooth' => 24, 'surface' => 'center', 'diagnosis' => 'filled',    'status' => 'completed'],
            ['tooth' => 36, 'surface' => 'center', 'diagnosis' => 'endodontic','status' => 'completed'],
            ['tooth' => 36, 'surface' => 'root',   'diagnosis' => 'endodontic','status' => 'completed'],
            ['tooth' => 46, 'surface' => 'center', 'diagnosis' => 'crown',     'status' => 'completed'],
            ['tooth' => 11, 'surface' => 'center', 'diagnosis' => 'missing',   'status' => 'completed'],
            ['tooth' => 21, 'surface' => 'center', 'diagnosis' => 'filled',    'status' => 'existing'],
        ];

        foreach ($patients as $patient) {
            $odontogram = \App\Models\Odontogram::create([
                'clinic_id' => $clinic->id,
                'patient_id' => $patient->id,
                'name' => 'Odontograma Inicial',
                'date' => now()->subDays(rand(1, 30)),
                'status' => 'in_progress',
            ]);

            foreach ($demoRecords as $rec) {
                // Find matching procedure for procedure_price_id
                $procedure = $procedures->firstWhere('diagnosis_code', $rec['diagnosis']);

                \App\Models\ClinicalRecord::create([
                    'clinic_id' => $clinic->id,
                    'patient_id' => $patient->id,
                    'odontogram_id' => $odontogram->id,
                    'tooth_number' => $rec['tooth'],
                    'surface' => $rec['surface'],
                    'diagnosis_code' => $rec['diagnosis'],
                    'procedure_price_id' => $procedure?->id,
                    'treatment_status' => $rec['status'],
                    'notes' => 'Registro demo',
                ]);
            }
        }
    }

    /**
     * Create appointments, budgets and payments for the given patients.
     */
    private function seedAppointmentsAndBudgets(Clinic $clinic, $patients, User $doctor, $procedures): void
    {
        foreach ($patients as $patient) {
            // Appointments
            for ($i = 0; $i < rand(1, 3); $i++) {
                $date = now()->subDays(rand(0, 60))->addDays(rand(0, 30));
                $procedure = $procedures->random();

                $appointment = \App\Models\Appointment::create([
                    'clinic_id' => $clinic->id,
                    'patient_id' => $patient->id,
                    'user_id' => $doctor->id,
                    'procedure_price_id' => $procedure->id,
                    'start_time' => $date->copy()->setTime(rand(9, 17), 0),
                    'end_time' => $date->copy()->setTime(rand(9, 17), 30),
                    'status' => $date->isPast() ? 'completed' : 'scheduled',
                    'type' => ['consultation', 'cleaning', 'surgery', 'control', 'urgent'][rand(0, 4)],
                ]);

                if ($appointment->status === 'completed') {
                    \App\Models\Treatment::create([
                        'clinic_id' => $clinic->id,
                        'appointment_id' => $appointment->id,
                        'name' => $procedure->procedure_name,
                        'price' => $procedure->price,
                        'code' => $procedure->code,
                        'created_at' => $appointment->end_time,
                    ]);
                }
            }

            // Budgets & Payments
            if (rand(0, 1)) {
                $accepted = (bool) rand(0, 1);

                // Select 2-3 real procedures for budget items
                $selectedProcs = $procedures->random(min(rand(2, 3), $procedures->count()));

                $budget = \App\Models\Budget::create([
                    'clinic_id' => $clinic->id,
                    'patient_id' => $patient->id,
                    'total' => $selectedProcs->sum('price'),
                    'status' => $accepted ? 'accepted' : 'sent',
                    'notes' => 'Presupuesto demo',
                    'expires_at' => now()->addDays(30),
                ]);

                foreach ($selectedProcs as $proc) {
                    \App\Models\BudgetItem::create([
                        'clinic_id' => $clinic->id,
                        'budget_id' => $budget->id,
                        'treatment_name' => $proc->procedure_name,
                        'cost' => $proc->price,
                        'quantity' => 1,
                        'procedure_price_id' => $proc->id,
                    ]);
                }

                if ($accepted) {
                    \App\Models\Payment::create([
                        'clinic_id' => $clinic->id,
                        'budget_id' => $budget->id,
                        'patient_id' => $patient->id,
                        'amount' => rand(
                            (int) ($budget->total * 0.4),
                            (int) $budget->total
                        ),
                        'method' => ['cash', 'card', 'transfer'][rand(0, 2)],
                        'paid_at' => now()->subMonths(rand(0, 11)),
                    ]);
                }
            }
        }
    }
}
