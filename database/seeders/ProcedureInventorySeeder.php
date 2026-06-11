<?php

namespace Database\Seeders;

use App\Models\ProcedurePrice;
use App\Models\Inventory;
use App\Models\ProcedureInventory;
use Illuminate\Database\Seeder;

class ProcedureInventorySeeder extends Seeder
{
    public function run(): void
    {
        $clinicId = tenant('id') ?? \App\Models\Clinic::first()?->id;

        if (!$clinicId) {
            $this->command->warn('No tenant context for ProcedureInventorySeeder.');
            return;
        }

        $procedures = ProcedurePrice::where('clinic_id', $clinicId)->get();
        $inventories = Inventory::where('clinic_id', $clinicId)->get();

        // Map procedures to inventory items they consume
        $mappings = [
            // diagnosis_code => [ [inventory_name_substring, quantity_used], ... ]
            'caries' => [
                ['Resina', 0.5],
                ['Adhesivo', 0.1],
                ['Ácido Grabador', 0.1],
                ['Guantes', 2],
            ],
            'filled' => [
                ['Resina', 0.3],
                ['Adhesivo', 0.1],
                ['Pulidores', 0.2],
                ['Guantes', 2],
            ],
            'endodontic' => [
                ['Limpiadores Endo K', 1],
                ['Conos de Gutapercha', 3],
                ['Cemento Sellador', 0.3],
                ['Hipoclorito', 0.1],
                ['Goma Dique', 1],
                ['Guantes', 2],
            ],
            'missing' => [
                ['Anestesia Lidocaína', 1],
                ['Agujas', 1],
                ['Elevador', 0.1],
                ['Fórcex', 0.1],
                ['Guantes', 2],
            ],
            'crown' => [
                ['Corona', 1],
                ['Adhesivo', 0.1],
                ['Guantes', 2],
            ],
            'prophylaxis' => [
                ['Pasta de Pulir', 0.2],
                ['Guantes', 2],
            ],
            'whitening' => [
                ['Gel Blanqueamiento', 1],
                ['Férulas', 1],
                ['Guantes', 2],
            ],
            'implant' => [
                ['Implante', 1],
                ['Anestesia Articaína', 2],
                ['Agujas', 2],
                ['Guantes', 2],
            ],
            'consultation' => [
                ['Guantes', 2],
            ],
            'xray_periapical' => [
                ['Película Radiográfica', 1],
            ],
            'xray_panoramic' => [
                ['Película Radiográfica', 1],
            ],
            'scaling' => [
                ['Cureta', 0.1],
                ['Anestesia Lidocaína', 1],
                ['Guantes', 2],
            ],
            'sealant' => [
                ['Sellante', 0.2],
                ['Ácido Grabador', 0.1],
                ['Guantes', 2],
            ],
            'braces_metal' => [
                ['Brackets Metálicos', 1],
                ['Arco Niti 0.014', 1],
                ['Adhesivo', 0.2],
                ['Guantes', 2],
            ],
            'braces_aesthetic' => [
                ['Brackets Estéticos', 1],
                ['Arco Niti 0.014', 1],
                ['Adhesivo', 0.2],
                ['Guantes', 2],
            ],
            'ortho_adjustment' => [
                ['Ligaduras', 0.2],
                ['Guantes', 1],
            ],
        ];

        $created = 0;

        foreach ($mappings as $diagnosisCode => $supplies) {
            $procedure = $procedures->firstWhere('diagnosis_code', $diagnosisCode);
            if (!$procedure) {
                continue;
            }

            foreach ($supplies as [$nameSubstring, $qty]) {
                $inventory = $inventories->first(fn($i) => stripos($i->name, (string) $nameSubstring) !== false);
                if (!$inventory) {
                    continue;
                }

                ProcedureInventory::firstOrCreate(
                    [
                        'clinic_id' => $clinicId,
                        'procedure_price_id' => $procedure->id,
                        'inventory_id' => $inventory->id,
                    ],
                    [
                        'quantity_used' => $qty,
                    ]
                );
                $created++;
            }
        }

        $this->command->info("✅ {$created} relaciones ProcedureInventory sembradas para: {$clinicId}");
    }
}
