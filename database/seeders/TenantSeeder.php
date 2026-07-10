<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\Clinic;
use App\Models\ClinicalRecord;
use App\Models\Odontogram;
use App\Models\Patient;
use App\Models\PatientMedicalHistory;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\ProcedurePrice;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $centralDomain = env('TENANCY_CENTRAL_DOMAINS', 'localhost');
        $centralDomain = trim(explode(',', $centralDomain)[0]);

        // ============================================================
        // CLINIC 1 — Clínica Dental Sonrisas (Pro, activa, data completa)
        // ============================================================
        $clinic1 = $this->createClinic('clinic1', 'Clínica Dental Sonrisas', 'USD', 'America/Caracas');
        $clinic1->domains()->firstOrCreate(['domain' => 'clinic1.localhost']);
        $clinic1->domains()->firstOrCreate(['domain' => "clinic1.{$centralDomain}"]);

        tenancy()->initialize($clinic1);
        setPermissionsTeamId($clinic1->id);

        $this->call(ProcedurePriceSeeder::class);
        $this->call(InventorySeeder::class);
        $this->call(ProcedureInventorySeeder::class);

        [$doc1, $assistant1] = $this->createClinicUsers($clinic1, 'admin@clinic1.com', 'Admin Sonrisas');

        $procedures = ProcedurePrice::where('clinic_id', $clinic1->id)->get();

        // Group procedures by diagnosis_code for quick lookup
        $procByCode = $procedures->groupBy('diagnosis_code')->map(fn ($g) => $g->first());

        $patients = $this->createPatients($clinic1, $doc1, 15);

        // Odontograms for first 8 patients — returns structured data for clinical flow
        $odontogramData = [];
        foreach ($patients->take(8) as $i => $patient) {
            $status = $i < 5 ? 'completed' : 'in_progress';
            $odontogramData[$patient->id] = $this->seedOdontogram($clinic1, $patient, $procByCode, $status);
        }

        $this->seedClinicalFlow($clinic1, $patients, $doc1, $procedures, $procByCode, $odontogramData);

        tenancy()->end();
        setPermissionsTeamId(null);

        // ============================================================
        // CLINIC 2 — Ortodoncia Pérez (Basic, activa, data básica)
        // ============================================================
        $clinic2 = $this->createClinic('clinic2', 'Ortodoncia Pérez', 'USD', 'America/Caracas');
        $clinic2->domains()->firstOrCreate(['domain' => 'clinic2.localhost']);
        $clinic2->domains()->firstOrCreate(['domain' => "clinic2.{$centralDomain}"]);

        tenancy()->initialize($clinic2);
        setPermissionsTeamId($clinic2->id);

        $this->call(ProcedurePriceSeeder::class);
        $this->call(InventorySeeder::class);
        $this->call(ProcedureInventorySeeder::class);

        [$doc2, $assistant2] = $this->createClinicUsers($clinic2, 'admin@clinic2.com', 'Admin Pérez');

        $proceduresC2 = ProcedurePrice::where('clinic_id', $clinic2->id)->get();
        $procByCodeC2 = $proceduresC2->groupBy('diagnosis_code')->map(fn ($g) => $g->first());
        $patientsC2 = $this->createPatients($clinic2, $doc2, 8);

        $odontogramDataC2 = [];
        foreach ($patientsC2->take(3) as $i => $patient) {
            $status = $i < 2 ? 'completed' : 'in_progress';
            $odontogramDataC2[$patient->id] = $this->seedOdontogram($clinic2, $patient, $procByCodeC2, $status);
        }

        $this->seedClinicalFlow($clinic2, $patientsC2, $doc2, $proceduresC2, $procByCodeC2, $odontogramDataC2);

        tenancy()->end();
        setPermissionsTeamId(null);

        // ============================================================
        // CLINIC 3 — Dental Trial (trialing, datos demo para explorar)
        // ============================================================
        $clinic3 = Clinic::firstOrCreate(['id' => 'clinic3'], ['name' => 'Dental Trial']);
        $clinic3->currency = 'Bs';
        $clinic3->timezone = 'America/Caracas';
        $clinic3->schedule_start = '09:00';
        $clinic3->schedule_end = '18:00';
        $clinic3->onboarding_step = 4;
        $clinic3->onboarding_completed_at = now()->toIso8601String();
        $clinic3->save();

        $clinic3->domains()->firstOrCreate(['domain' => 'clinic3.localhost']);
        $clinic3->domains()->firstOrCreate(['domain' => "clinic3.{$centralDomain}"]);

        tenancy()->initialize($clinic3);
        setPermissionsTeamId($clinic3->id);

        $this->call(ProcedurePriceSeeder::class);
        $this->call(InventorySeeder::class);
        $this->call(ProcedureInventorySeeder::class);

        [$doc3, $assistant3] = $this->createClinicUsers($clinic3, 'admin@clinic3.com', 'Admin Trial');

        tenancy()->end();
        setPermissionsTeamId(null);

        // ============================================================
        // CLINIC 4 — Dental Suspendida (suspendida, sin actividad nueva)
        // ============================================================
        $clinic4 = Clinic::firstOrCreate(['id' => 'clinic4'], ['name' => 'Dental Suspendida']);
        $clinic4->currency = 'USD';
        $clinic4->timezone = 'America/Bogota';
        $clinic4->schedule_start = '08:00';
        $clinic4->schedule_end = '16:00';
        $clinic4->onboarding_step = 4;
        $clinic4->onboarding_completed_at = now()->subMonths(3)->toIso8601String();
        $clinic4->save();

        $clinic4->domains()->firstOrCreate(['domain' => 'clinic4.localhost']);
        $clinic4->domains()->firstOrCreate(['domain' => "clinic4.{$centralDomain}"]);

        tenancy()->initialize($clinic4);
        setPermissionsTeamId($clinic4->id);

        [$doc4, $assistant4] = $this->createClinicUsers($clinic4, 'admin@clinic4.com', 'Admin Suspendido');

        $this->call(ProcedurePriceSeeder::class);
        $this->call(InventorySeeder::class);

        $proceduresC4 = ProcedurePrice::where('clinic_id', $clinic4->id)->get();
        $patientsC4 = $this->createPatients($clinic4, $doc4, 3);

        $this->seedPastAppointmentsOnly($clinic4, $patientsC4, $proceduresC4, $doc4);

        tenancy()->end();
        setPermissionsTeamId(null);
    }

    private function createClinic(string $id, string $name, string $currency, string $timezone): Clinic
    {
        $clinic = Clinic::firstOrCreate(['id' => $id], ['name' => $name]);
        $clinic->currency = $currency;
        $clinic->timezone = $timezone;
        $clinic->schedule_start = '09:00';
        $clinic->schedule_end = '18:00';
        $clinic->onboarding_step = 4;
        $clinic->onboarding_completed_at = now()->toIso8601String();
        $clinic->save();

        return $clinic;
    }

    private function createClinicUsers(Clinic $clinic, string $adminEmail, string $adminName): array
    {
        $admin = User::firstOrCreate(['email' => $adminEmail], [
            'name' => $adminName,
            'password' => Hash::make('password'),
            'clinic_id' => $clinic->id,
        ]);
        $admin->assignRole('admin');

        $doctor = User::firstOrCreate(['email' => "doctor@{$clinic->id}.com"], [
            'name' => "Dr. {$clinic->name}",
            'password' => Hash::make('password'),
            'clinic_id' => $clinic->id,
        ]);
        $doctor->assignRole('doctor');

        $assistant = User::firstOrCreate(['email' => "assistant@{$clinic->id}.com"], [
            'name' => "Asistente {$clinic->name}",
            'password' => Hash::make('password'),
            'clinic_id' => $clinic->id,
        ]);
        $assistant->assignRole('assistant');

        return [$doctor, $assistant];
    }

    private function createPatients(Clinic $clinic, ?User $doctor, int $count): Collection
    {
        $patients = collect();

        for ($i = 0; $i < $count; $i++) {
            $patient = Patient::factory()->create([
                'clinic_id' => $clinic->id,
                'doctor_id' => $doctor?->id,
            ]);

            $this->createMedicalHistory($patient);
            $patients->push($patient);
        }

        return $patients;
    }

    private function createMedicalHistory(Patient $patient): void
    {
        static $histories = [
            [
                'antecedentes_personales' => 'Paciente refiere hipertensión arterial controlada con Losartán 50mg. Niega diabetes. Cirugía de apendicitis en 2018.',
                'antecedentes_familiares' => 'Madre hipertensa y diabética tipo 2. Padre fallecido por infarto a los 72 años.',
                'alergias' => 'Penicilina — causa urticaria generalizada. Ibuprofeno — causa gastritis.',
                'medicamentos_actuales' => 'Losartán 50mg cada 24h',
                'enfermedades_cronicas' => 'Hipertensión arterial esencial (CIE-10: I10)',
                'habitos' => 'Fumador social (3-4 cigarrillos/semana). No consume alcohol.',
                'cirugias_previas' => 'Apendicectomía laparoscópica (2018)',
                'historia_dental' => 'Tratamientos de caries en piezas 16, 26 y 46 hace 3 años. Limpieza dental anual irregular.',
                'presion_arterial' => '130/85',
                'frecuencia_cardiaca' => '78',
                'peso' => 78.5,
                'altura' => 1.72,
                'grupo_sanguineo' => 'O+',
            ],
            [
                'antecedentes_personales' => 'Paciente sana. Niega enfermedades crónicas. Sin cirugías previas.',
                'antecedentes_familiares' => 'Padres aparentemente sanos. Abuela materna con diabetes tipo 2.',
                'alergias' => 'Látex — causa dermatitis de contacto. Sin alergias medicamentosas conocidas.',
                'medicamentos_actuales' => 'Ninguno',
                'enfermedades_cronicas' => 'Ninguna',
                'habitos' => 'No fuma. Consume alcohol socialmente (fines de semana). Se cepilla 2 veces al día.',
                'cirugias_previas' => 'Ninguna',
                'historia_dental' => 'Tratamiento ortodóntico completo hace 5 años. Usa retenedor nocturno. Sellantes en molares.',
                'presion_arterial' => '118/72',
                'frecuencia_cardiaca' => '72',
                'peso' => 62.0,
                'altura' => 1.65,
                'grupo_sanguineo' => 'A-',
            ],
            [
                'antecedentes_personales' => 'Diabetes tipo 2 diagnosticada hace 4 años. Controlada con Metformina 850mg cada 12h. Hipotiroidismo en tratamiento con Levotiroxina 75mcg.',
                'antecedentes_familiares' => 'Madre diabética. Hermano con hipotiroidismo.',
                'alergias' => 'Sulfamidas — causa rash cutáneo. Aspirina - causa broncoespasmo.',
                'medicamentos_actuales' => 'Metformina 850mg c/12h, Levotiroxina 75mcg c/24h en ayunas',
                'enfermedades_cronicas' => 'Diabetes mellitus tipo 2 (CIE-10: E11), Hipotiroidismo (CIE-10: E03)',
                'habitos' => 'No fuma. No consume alcohol. Dieta controlada para diabetes.',
                'cirugias_previas' => 'Cesárea (2019)',
                'historia_dental' => 'Enfermedad periodontal leve diagnosticada en última consulta. Sensibilidad dental en cuellos expuestos.',
                'presion_arterial' => '125/80',
                'frecuencia_cardiaca' => '80',
                'peso' => 85.0,
                'altura' => 1.60,
                'grupo_sanguineo' => 'B+',
            ],
            [
                'antecedentes_personales' => 'Asma bronquial leve. Controlada con Salbutamol PRN. Rinitis alérgica estacional.',
                'antecedentes_familiares' => 'Padre con asma. Madre con hipertensión.',
                'alergias' => 'Polen — rinitis alérgica. Ácaros — asma. Penicilina — rash.',
                'medicamentos_actuales' => 'Salbutamol inhalador PRN. Loratadina 10mg en temporada alérgica.',
                'enfermedades_cronicas' => 'Asma bronquial leve (CIE-10: J45)',
                'habitos' => 'No fuma. No alcohol. Practica natación 3 veces por semana.',
                'cirugias_previas' => 'Amigdalectomía (2010)',
                'historia_dental' => 'Bruxismo severo nocturno. Portador de férula oclusal desde 2022. Caries recurrentes.',
                'presion_arterial' => '120/78',
                'frecuencia_cardiaca' => '65',
                'peso' => 70.0,
                'altura' => 1.75,
                'grupo_sanguineo' => 'AB-',
            ],
            [
                'antecedentes_personales' => 'Paciente sano. Deportista de alto rendimiento (triatlón). Niega enfermedades crónicas.',
                'antecedentes_familiares' => 'Padres sanos. Hermano con asma.',
                'alergias' => 'Sin alergias conocidas.',
                'medicamentos_actuales' => 'Suplementos deportivos (proteína, creatina). Sin medicamentos.',
                'enfermedades_cronicas' => 'Ninguna',
                'habitos' => 'No fuma. No alcohol. Dieta alta en proteínas. Se cepila 3 veces al día.',
                'cirugias_previas' => 'Reducción de fractura de clavícula (2021)',
                'historia_dental' => 'Traumatismo dental en pieza 21 por caída en bicicleta (2021). Tratada con resina compuesta. Vigilar vitalidad.',
                'presion_arterial' => '115/70',
                'frecuencia_cardiaca' => '55',
                'peso' => 75.0,
                'altura' => 1.83,
                'grupo_sanguineo' => 'O-',
            ],
        ];

        $base = $histories[$patient->id % count($histories)];

        PatientMedicalHistory::create([
            'clinic_id' => $patient->clinic_id,
            'patient_id' => $patient->id,
            ...$base,
            'peso' => $base['peso'] + fake()->randomFloat(1, -5, 5),
        ]);
    }

    /**
     * Seed an odontogram and return structured clinical record data.
     *
     * @return array List of ['procedure_price_id', 'diagnosis_code', 'treatment_status', 'tooth_number', 'procedure']
     */
    private function seedOdontogram(Clinic $clinic, Patient $patient, Collection $procByCode, string $status): array
    {
        static $odontogramPatterns = [
            [
                ['tooth' => 16, 'surface' => 'center', 'diagnosis' => 'caries', 'notes' => 'Caries oclusal profunda en esmalte/dentina'],
                ['tooth' => 16, 'surface' => 'top', 'diagnosis' => 'caries', 'notes' => 'Caries proximal mesial'],
                ['tooth' => 26, 'surface' => 'center', 'diagnosis' => 'caries', 'notes' => 'Caries recurrente bajo obturación existente'],
                ['tooth' => 36, 'surface' => 'center', 'diagnosis' => 'endodontic', 'notes' => 'Endodoncia previa con buen sellado apical'],
                ['tooth' => 46, 'surface' => 'center', 'diagnosis' => 'crown', 'notes' => 'Corona metal-cerámica en buen estado'],
                ['tooth' => 11, 'surface' => 'center', 'diagnosis' => 'filled', 'notes' => 'Restauración clase IV en composite, 2 años'],
                ['tooth' => 21, 'surface' => 'center', 'diagnosis' => 'filled', 'notes' => 'Restauración estética en composite'],
                ['tooth' => 14, 'surface' => 'top', 'diagnosis' => 'caries', 'notes' => 'Caries proximal incipiente'],
            ],
            [
                ['tooth' => 24, 'surface' => 'center', 'diagnosis' => 'caries', 'notes' => 'Caries oclusal pequeña, control radiográfico'],
                ['tooth' => 25, 'surface' => 'center', 'diagnosis' => 'caries', 'notes' => 'Caries secundaria en margen de obturación'],
                ['tooth' => 34, 'surface' => 'center', 'diagnosis' => 'filled', 'notes' => 'Obturación clase I en amalgama, 5 años'],
                ['tooth' => 35, 'surface' => 'center', 'diagnosis' => 'filled', 'notes' => 'Obturación en resina compuesta, 2 años'],
                ['tooth' => 44, 'surface' => 'center', 'diagnosis' => 'caries', 'notes' => 'Caries radicular en superficie vestibular'],
                ['tooth' => 45, 'surface' => 'center', 'diagnosis' => 'missing', 'notes' => 'Ausente por extracción previa, espacio cerrado'],
                ['tooth' => 17, 'surface' => 'center', 'diagnosis' => 'healthy', 'notes' => 'Pieza sana'],
                ['tooth' => 27, 'surface' => 'center', 'diagnosis' => 'healthy', 'notes' => 'Pieza sana'],
            ],
            [
                ['tooth' => 36, 'surface' => 'center', 'diagnosis' => 'missing', 'notes' => 'Ausente por caries, pendiente de implante'],
                ['tooth' => 37, 'surface' => 'center', 'diagnosis' => 'caries', 'notes' => 'Caries distal profunda con posible compromiso pulpar'],
                ['tooth' => 46, 'surface' => 'center', 'diagnosis' => 'endodontic', 'notes' => 'Endodoncia multirradicular finalizada hace 1 mes'],
                ['tooth' => 47, 'surface' => 'top', 'diagnosis' => 'caries', 'notes' => 'Caries proximal mesial con pérdida de punto de contacto'],
                ['tooth' => 14, 'surface' => 'center', 'diagnosis' => 'filled', 'notes' => 'Restauración clase II en buen estado'],
                ['tooth' => 15, 'surface' => 'center', 'diagnosis' => 'crown', 'notes' => 'Corona completa de zirconio, recién cementada'],
                ['tooth' => 21, 'surface' => 'center', 'diagnosis' => 'healthy', 'notes' => 'Pieza sana'],
                ['tooth' => 31, 'surface' => 'center', 'diagnosis' => 'healthy', 'notes' => 'Pieza sana'],
            ],
        ];

        $pattern = $odontogramPatterns[$patient->id % count($odontogramPatterns)];

        $odontogram = Odontogram::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'name' => 'Odontograma '.($status === 'completed' ? 'Inicial' : 'Evaluación'),
            'date' => $status === 'completed' ? now()->subDays(rand(5, 20)) : now()->subDays(rand(0, 3)),
            'status' => $status,
        ]);

        $records = [];

        foreach ($pattern as $rec) {
            $procedure = $procByCode->get($rec['diagnosis']);

            // Completed odontogram: non-caries findings are past work; caries are pending
            $treatmentStatus = ($status === 'completed' && ! in_array($rec['diagnosis'], ['caries', 'healthy', 'missing'], true))
                ? 'completed'
                : 'planned';

            ClinicalRecord::create([
                'clinic_id' => $clinic->id,
                'patient_id' => $patient->id,
                'odontogram_id' => $odontogram->id,
                'tooth_number' => $rec['tooth'],
                'surface' => $rec['surface'],
                'diagnosis_code' => $rec['diagnosis'],
                'procedure_price_id' => $procedure?->id,
                'treatment_status' => $treatmentStatus,
                'notes' => $rec['notes'] ?? 'Registro clínico',
            ]);

            $records[] = [
                'procedure_price_id' => $procedure?->id,
                'diagnosis_code' => $rec['diagnosis'],
                'treatment_status' => $treatmentStatus,
                'tooth_number' => $rec['tooth'],
                'procedure' => $procedure,
            ];
        }

        return $records;
    }

    /**
     * Create appointments, treatments, budgets, payments, and prescriptions.
     *
     * For patients WITH odontogram data: appointments/treatments match the clinical findings.
     * For patients WITHOUT odontogram data: random procedures (walk-in / emergency).
     */
    private function seedClinicalFlow(Clinic $clinic, $patients, User $doctor, $procedures, Collection $procByCode, array $odontogramData = []): void
    {
        foreach ($patients as $patient) {
            $hasOdontogram = isset($odontogramData[$patient->id]);
            $records = $hasOdontogram ? $odontogramData[$patient->id] : [];

            // ---- APPOINTMENTS & TREATMENTS ----
            if ($hasOdontogram) {
                // Completed records → past appointments + treatments
                $completedRecords = array_filter($records, fn ($r) => $r['treatment_status'] === 'completed' && $r['procedure']);
                foreach ($completedRecords as $rec) {
                    $pastDate = now()->subDays(rand(5, 30));
                    $pastDate = $pastDate->setTime(rand(9, 16), 0);
                    $proc = $rec['procedure'];

                    $apptId = DB::table('appointments')->insertGetId([
                        'clinic_id' => $clinic->id,
                        'patient_id' => $patient->id,
                        'user_id' => $doctor->id,
                        'procedure_price_id' => $proc->id,
                        'start_time' => $pastDate,
                        'end_time' => $pastDate->copy()->addMinutes((int) ($proc->duration ?? 30)),
                        'status' => 'completed',
                        'type' => 'consultation',
                        'notes' => $rec['diagnosis_code'] === 'endodontic' ? 'Tratamiento de conducto realizado' : 'Procedimiento completado',
                        'created_at' => $pastDate->copy()->subDay(),
                        'updated_at' => now(),
                    ]);

                    Treatment::create([
                        'clinic_id' => $clinic->id,
                        'appointment_id' => $apptId,
                        'name' => $proc->procedure_name,
                        'price' => $proc->price,
                        'code' => $proc->code,
                    ]);
                }

                // Planned records with caries → future appointments
                $plannedCures = array_filter($records, fn ($r) => $r['treatment_status'] === 'planned' && in_array($r['diagnosis_code'], ['caries'], true) && $r['procedure']);
                $plannedIdx = 0;
                foreach ($plannedCures as $rec) {
                    if ($plannedIdx >= 3) {
                        break;
                    } // max 3 future appointments
                    $futureDate = now()->addDays(rand(1, 30) + ($plannedIdx * 3));
                    $futureDate = $futureDate->setTime(rand(9, 16), 0);
                    $proc = $rec['procedure'];

                    Appointment::create([
                        'clinic_id' => $clinic->id,
                        'patient_id' => $patient->id,
                        'user_id' => $doctor->id,
                        'procedure_price_id' => $proc->id,
                        'start_time' => $futureDate,
                        'end_time' => $futureDate->copy()->addMinutes((int) ($proc->duration ?? 30)),
                        'status' => $plannedIdx === 0 ? 'confirmed' : 'scheduled',
                        'type' => 'consultation',
                        'notes' => 'Tratamiento de caries programado',
                    ]);
                    $plannedIdx++;
                }

                // Extra random future appointment for variety
                if (rand(0, 1)) {
                    $futureDate = now()->addDays(rand(5, 40));
                    $futureDate = $futureDate->setTime(rand(9, 16), 0);
                    $proc = $procedures->random();

                    Appointment::create([
                        'clinic_id' => $clinic->id,
                        'patient_id' => $patient->id,
                        'user_id' => $doctor->id,
                        'procedure_price_id' => $proc->id,
                        'start_time' => $futureDate,
                        'end_time' => $futureDate->copy()->addMinutes((int) ($proc->duration ?? 30)),
                        'status' => 'scheduled',
                        'type' => ['control', 'cleaning', 'urgent'][array_rand(['control', 'cleaning', 'urgent'])],
                        'notes' => null,
                    ]);
                }
            } else {
                // No odontogram — random walk-in appointments (past + future)
                $numPast = rand(1, 2);
                for ($j = 0; $j < $numPast; $j++) {
                    $pastDate = now()->subDays(rand(5, 45) + ($j * 5));
                    $pastDate = $pastDate->setTime(rand(9, 16), 0);
                    $proc = $procedures->random();

                    $apptId = DB::table('appointments')->insertGetId([
                        'clinic_id' => $clinic->id,
                        'patient_id' => $patient->id,
                        'user_id' => $doctor->id,
                        'procedure_price_id' => $proc->id,
                        'start_time' => $pastDate,
                        'end_time' => $pastDate->copy()->addMinutes((int) ($proc->duration ?? 30)),
                        'status' => 'completed',
                        'type' => ['consultation', 'urgent', 'cleaning'][array_rand(['consultation', 'urgent', 'cleaning'])],
                        'notes' => 'Consulta sin odontograma previo',
                        'created_at' => $pastDate->copy()->subDay(),
                        'updated_at' => now(),
                    ]);

                    Treatment::create([
                        'clinic_id' => $clinic->id,
                        'appointment_id' => $apptId,
                        'name' => $proc->procedure_name,
                        'price' => $proc->price,
                        'code' => $proc->code,
                    ]);
                }

                $numFuture = rand(1, 2);
                for ($j = 0; $j < $numFuture; $j++) {
                    $futureDate = now()->addDays(rand(1, 30) + ($j * 5));
                    $futureDate = $futureDate->setTime(rand(9, 16), 0);
                    $proc = $procedures->random();

                    Appointment::create([
                        'clinic_id' => $clinic->id,
                        'patient_id' => $patient->id,
                        'user_id' => $doctor->id,
                        'procedure_price_id' => $proc->id,
                        'start_time' => $futureDate,
                        'end_time' => $futureDate->copy()->addMinutes((int) ($proc->duration ?? 30)),
                        'status' => $j === 0 ? 'confirmed' : 'scheduled',
                        'type' => ['consultation', 'control', 'cleaning'][array_rand(['consultation', 'control', 'cleaning'])],
                        'notes' => null,
                    ]);
                }
            }

            // ---- BUDGETS (based on odontogram if available) ----
            if ($hasOdontogram) {
                // Planned caries → create a budget
                $plannedTreatments = array_filter($records, fn ($r) => $r['treatment_status'] === 'planned' && $r['diagnosis_code'] === 'caries' && $r['procedure']);

                if (count($plannedTreatments) > 0 && rand(1, 10) <= 8) {
                    $budgetStatuses = ['sent', 'accepted', 'accepted', 'draft'];
                    $budgetStatus = $budgetStatuses[array_rand($budgetStatuses)];
                    $total = 0;
                    $items = [];

                    foreach ($plannedTreatments as $rec) {
                        $qty = 1;
                        $total += $rec['procedure']->price * $qty;
                        $items[] = ['proc' => $rec['procedure'], 'qty' => $qty];
                    }

                    if (count($items) > 0) {
                        $budget = Budget::create([
                            'clinic_id' => $clinic->id,
                            'patient_id' => $patient->id,
                            'user_id' => $doctor->id,
                            'total' => $total,
                            'status' => $budgetStatus,
                            'notes' => $budgetStatus === 'draft' ? 'Presupuesto en preparación' : 'Plan de tratamiento basado en odontograma',
                            'expires_at' => now()->addDays(30),
                        ]);

                        foreach ($items as $item) {
                            BudgetItem::create([
                                'clinic_id' => $clinic->id,
                                'budget_id' => $budget->id,
                                'treatment_name' => $item['proc']->procedure_name,
                                'cost' => $item['proc']->price,
                                'quantity' => $item['qty'],
                                'procedure_price_id' => $item['proc']->id,
                            ]);
                        }

                        if ($budgetStatus === 'accepted') {
                            $paymentMethods = ['cash', 'transfer', 'card'];
                            $paidAmount = rand(0, 1) ? $total : (int) ($total * rand(4, 8) / 10);

                            Payment::create([
                                'clinic_id' => $clinic->id,
                                'budget_id' => $budget->id,
                                'patient_id' => $patient->id,
                                'amount' => $paidAmount,
                                'method' => $paymentMethods[array_rand($paymentMethods)],
                                'status' => 'completed',
                                'paid_at' => now()->subDays(rand(1, 15)),
                            ]);
                        }
                    }
                }
            } elseif (rand(1, 10) <= 5) {
                // No odontogram — random budget ~50% of the time
                $budgetStatuses = ['draft', 'sent', 'accepted', 'rejected'];
                $budgetStatus = $budgetStatuses[array_rand($budgetStatuses)];
                $selectedProcs = $procedures->random(min(rand(2, 3), $procedures->count()));
                $total = $selectedProcs->sum('price');

                $budget = Budget::create([
                    'clinic_id' => $clinic->id,
                    'patient_id' => $patient->id,
                    'user_id' => $doctor->id,
                    'total' => $total,
                    'status' => $budgetStatus,
                    'notes' => 'Presupuesto general',
                    'expires_at' => now()->addDays(30),
                ]);

                foreach ($selectedProcs as $proc) {
                    BudgetItem::create([
                        'clinic_id' => $clinic->id,
                        'budget_id' => $budget->id,
                        'treatment_name' => $proc->procedure_name,
                        'cost' => $proc->price,
                        'quantity' => 1,
                        'procedure_price_id' => $proc->id,
                    ]);
                }

                if ($budgetStatus === 'accepted') {
                    $paymentMethods = ['cash', 'transfer', 'card'];
                    $paidAmount = rand(0, 1) ? $total : (int) ($total * rand(4, 8) / 10);

                    Payment::create([
                        'clinic_id' => $clinic->id,
                        'budget_id' => $budget->id,
                        'patient_id' => $patient->id,
                        'amount' => $paidAmount,
                        'method' => $paymentMethods[array_rand($paymentMethods)],
                        'status' => 'completed',
                        'paid_at' => now()->subDays(rand(1, 15)),
                    ]);
                }
            }

            // ---- PRESCRIPTIONS for ~40% of patients ----
            if (rand(1, 10) <= 4) {
                $this->seedPrescription($clinic, $patient, $doctor);
            }
        }
    }

    private function seedPrescription(Clinic $clinic, Patient $patient, User $doctor): void
    {
        static $prescriptions = [
            [
                'diagnosis' => 'Infección post-extracción de tercer molar',
                'items' => [
                    ['medication' => 'Amoxicilina 500mg', 'dosage' => '1 cápsula', 'frequency' => 'Cada 8 horas por 7 días', 'duration' => '7 días', 'quantity' => 21, 'indications' => 'Tomar con alimentos'],
                    ['medication' => 'Ibuprofeno 600mg', 'dosage' => '1 tableta', 'frequency' => 'Cada 8 horas si dolor', 'duration' => '5 días', 'quantity' => 15, 'indications' => 'No exceder 3 dosis al día'],
                    ['medication' => 'Clorhexidina 0.12%', 'dosage' => '10ml', 'frequency' => 'Enjuague cada 12 horas', 'duration' => '10 días', 'quantity' => 1, 'indications' => 'No diluir. No ingerir. Usar tras el cepillado'],
                ],
            ],
            [
                'diagnosis' => 'Tratamiento de conducto radicular — pulpitis irreversible',
                'items' => [
                    ['medication' => 'Amoxicilina + Ácido Clavulánico 875/125mg', 'dosage' => '1 tableta', 'frequency' => 'Cada 12 horas', 'duration' => '7 días', 'quantity' => 14, 'indications' => 'Tomar con alimentos para evitar molestias gástricas'],
                    ['medication' => 'Naproxeno Sódico 550mg', 'dosage' => '1 tableta', 'frequency' => 'Cada 12 horas', 'duration' => '5 días', 'quantity' => 10, 'indications' => 'Tomar si hay dolor post-operatorio'],
                    ['medication' => 'Diclofenac Gel 1%', 'dosage' => 'Aplicar cantidad suficiente', 'frequency' => 'Cada 8 horas', 'duration' => '5 días', 'quantity' => 1, 'indications' => 'Masajear suavemente la zona afectada'],
                ],
            ],
            [
                'diagnosis' => 'Gingivitis aguda — periodontitis leve',
                'items' => [
                    ['medication' => 'Metronidazol 500mg', 'dosage' => '1 tableta', 'frequency' => 'Cada 8 horas', 'duration' => '7 días', 'quantity' => 21, 'indications' => 'No consumir alcohol durante el tratamiento'],
                    ['medication' => 'Clorhexidina 0.12%', 'dosage' => '10ml', 'frequency' => 'Enjuague cada 12 horas', 'duration' => '14 días', 'quantity' => 1, 'indications' => 'Enjuagar por 30 segundos. No enjuagar con agua después'],
                    ['medication' => 'Hilo Dental', 'dosage' => 'Una vez al día', 'frequency' => 'Cada noche', 'duration' => 'Uso continuo', 'quantity' => 1, 'indications' => 'Usar antes del cepillado nocturno'],
                ],
            ],
            [
                'diagnosis' => 'Absceso dentoalveolar — celulitis facial leve',
                'items' => [
                    ['medication' => 'Clindamicina 300mg', 'dosage' => '1 cápsula', 'frequency' => 'Cada 6 horas', 'duration' => '10 días', 'quantity' => 40, 'indications' => 'Completar el ciclo completo aunque mejoren los síntomas'],
                    ['medication' => 'Metamizol 500mg', 'dosage' => '1 tableta', 'frequency' => 'Cada 8 horas si dolor intenso', 'duration' => '5 días', 'quantity' => 15, 'indications' => 'No exceder 3 dosis al día'],
                    ['medication' => 'Dexametasona 8mg', 'dosage' => '1 tableta', 'frequency' => 'Cada 24 horas por 3 días', 'duration' => '3 días', 'quantity' => 3, 'indications' => 'Tomar en ayunas para reducir inflamación'],
                ],
            ],
        ];

        $rx = $prescriptions[$patient->id % count($prescriptions)];
        $statuses = ['active', 'active', 'active', 'completed'];
        $status = $statuses[array_rand($statuses)];

        $prescription = Prescription::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'diagnosis' => $rx['diagnosis'],
            'status' => $status,
            'signed_at' => $status === 'completed' ? now()->subDays(rand(5, 30)) : now(),
        ]);

        foreach ($rx['items'] as $item) {
            PrescriptionItem::create([
                'clinic_id' => $clinic->id,
                'prescription_id' => $prescription->id,
                ...$item,
            ]);
        }
    }

    private function seedPastAppointmentsOnly(Clinic $clinic, $patients, $procedures, ?User $doctor = null): void
    {
        foreach ($patients as $patient) {
            $numPast = rand(1, 2);
            for ($j = 0; $j < $numPast; $j++) {
                $pastDate = now()->subMonths(rand(2, 4))->addDays($j * 7);
                $pastDate = $pastDate->setTime(rand(9, 16), 0);
                $proc = $procedures->random();

                $apptId = DB::table('appointments')->insertGetId([
                    'clinic_id' => $clinic->id,
                    'patient_id' => $patient->id,
                    'user_id' => $doctor?->id,
                    'procedure_price_id' => $proc->id,
                    'start_time' => $pastDate,
                    'end_time' => $pastDate->copy()->addMinutes((int) ($proc->duration ?? 30)),
                    'status' => 'completed',
                    'type' => ['consultation', 'cleaning', 'control'][array_rand(['consultation', 'cleaning', 'control'])],
                    'notes' => 'Cita histórica previa a suspensión',
                    'created_at' => $pastDate->copy()->subDay(),
                    'updated_at' => $pastDate,
                ]);

                Treatment::create([
                    'clinic_id' => $clinic->id,
                    'appointment_id' => $apptId,
                    'name' => $proc->procedure_name,
                    'price' => $proc->price,
                    'code' => $proc->code,
                ]);
            }
        }
    }
}
