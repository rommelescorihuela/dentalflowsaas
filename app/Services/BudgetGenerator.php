<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\Odontogram;
use App\Models\ProcedurePrice;
use Illuminate\Support\Facades\DB;

class BudgetGenerator
{
    protected array $diagnosisDefaults = [
        'caries' => ['name' => 'Obturación/Resellado', 'multiplier' => 1.0],
        'filled' => ['name' => 'Restauración', 'multiplier' => 0.8],
        'endodontic' => ['name' => 'Endodoncia', 'multiplier' => 1.5],
        'missing' => ['name' => 'Extracción', 'multiplier' => 1.0],
        'crown' => ['name' => 'Corona', 'multiplier' => 1.8],
        'healthy' => ['name' => 'Revisión', 'multiplier' => 0.3],
    ];

    public function generate(Odontogram $odontogram): Budget
    {
        return DB::transaction(function () use ($odontogram) {
            $existing = Budget::where('odontogram_id', $odontogram->id)->first();
            if ($existing) {
                return $existing;
            }

            $records = $odontogram->clinicalRecords()
                ->where('treatment_status', '!=', 'completed')
                ->get();

            if ($records->isEmpty()) {
                return Budget::create([
                    'clinic_id' => $odontogram->clinic_id,
                    'patient_id' => $odontogram->patient_id,
                    'odontogram_id' => $odontogram->id,
                    'total' => 0,
                    'status' => 'draft',
                    'notes' => 'Presupuesto generado automáticamente desde odontograma sin registros pendientes.',
                ]);
            }

            // OPTIMIZACIÓN: Cargar todos los procedure prices de una vez para evitar N+1
            $procedurePriceIds = $records->pluck('procedure_price_id')->filter()->unique();
            $procedurePrices = ProcedurePrice::whereIn('id', $procedurePriceIds)
                ->get()
                ->keyBy('id');

            // También cargamos los procedimientos por diagnosis_code para fallback
            $diagnosisCodes = $records->pluck('diagnosis_code')->filter()->unique();
            $proceduresByDiagnosis = ProcedurePrice::where('clinic_id', $odontogram->clinic_id)
                ->whereIn('diagnosis_code', $diagnosisCodes)
                ->get()
                ->groupBy('diagnosis_code');

            $groupedItems = [];
            $total = 0;

            foreach ($records as $record) {
                $procedure = null;

                // First try to get the exact procedure from the clinical record (usando cache)
                if ($record->procedure_price_id && isset($procedurePrices[$record->procedure_price_id])) {
                    $procedure = $procedurePrices[$record->procedure_price_id];
                }

                // Fallback to diagnosis code lookup (usando cache)
                if (!$procedure && $record->diagnosis_code && isset($proceduresByDiagnosis[$record->diagnosis_code])) {
                    $procedure = $proceduresByDiagnosis[$record->diagnosis_code]->first();
                }

                if ($procedure) {
                    $key = 'proc_' . $procedure->id;
                    $name = $procedure->procedure_name;
                    $cost = $procedure->price;
                    $procedurePriceId = $procedure->id;
                } else {
                    $default = $this->diagnosisDefaults[$record->diagnosis_code] ?? null;
                    $key = 'default_' . ($record->diagnosis_code ?? 'unknown');
                    $cost = $default ? 50000 * $default['multiplier'] : 50000;
                    $name = $default['name'] ?? 'Tratamiento';
                    $procedurePriceId = null;
                }

                if (!isset($groupedItems[$key])) {
                    $groupedItems[$key] = [
                        'treatment_name' => $name,
                        'cost' => $cost,
                        'quantity' => 0,
                        'procedure_price_id' => $procedurePriceId,
                        'teeth' => [],
                    ];
                }

                $groupedItems[$key]['quantity']++;
                $groupedItems[$key]['teeth'][] = $record->tooth_number;
                $total += $cost;
            }

            $budget = Budget::create([
                'clinic_id' => $odontogram->clinic_id,
                'patient_id' => $odontogram->patient_id,
                'odontogram_id' => $odontogram->id,
                'total' => $total,
                'status' => 'draft',
                'notes' => 'Presupuesto generado automáticamente desde odontograma #' . $odontogram->id . '.',
                'expires_at' => now()->addDays(30),
            ]);

            foreach ($groupedItems as $item) {
                $teethList = implode(', ', array_unique($item['teeth']));
                $treatmentName = $item['treatment_name'] . ' (Dientes: ' . $teethList . ')';

                $budget->items()->create([
                    'clinic_id' => $odontogram->clinic_id,
                    'treatment_name' => $treatmentName,
                    'cost' => $item['cost'],
                    'quantity' => $item['quantity'],
                    'procedure_price_id' => $item['procedure_price_id'],
                ]);
            }

            return $budget;
        });
    }
}
