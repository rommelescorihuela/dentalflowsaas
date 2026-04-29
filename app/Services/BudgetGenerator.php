<?php

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

            $groupedItems = [];
            $total = 0;

            foreach ($records as $record) {
                $procedure = null;

                // First try to get the exact procedure from the clinical record
                if ($record->procedure_price_id) {
                    $procedure = ProcedurePrice::find($record->procedure_price_id);
                }

                // Fallback to diagnosis code lookup
                if (!$procedure) {
                    $procedure = $this->findProcedure($odontogram->clinic_id, $record->diagnosis_code);
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

    protected function findProcedure(string $clinicId, ?string $diagnosisCode): ?ProcedurePrice
    {
        if (!$diagnosisCode) {
            return null;
        }

        return ProcedurePrice::where('clinic_id', $clinicId)
            ->where('diagnosis_code', $diagnosisCode)
            ->first();
    }
}
