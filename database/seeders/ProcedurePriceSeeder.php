<?php

namespace Database\Seeders;

use App\Models\ProcedurePrice;
use Illuminate\Database\Seeder;

class ProcedurePriceSeeder extends Seeder
{
    protected array $diagnosisMapping = [
        [
            'procedure_name' => 'Obturación/Resellado',
            'diagnosis_code' => 'caries',
            'price' => 50000,
            'duration' => 45,
            'description' => 'Tratamiento de caries con resina composite',
        ],
        [
            'procedure_name' => 'Restauración',
            'diagnosis_code' => 'filled',
            'price' => 40000,
            'duration' => 30,
            'description' => 'Restauración de pieza dental',
        ],
        [
            'procedure_name' => 'Endodoncia',
            'diagnosis_code' => 'endodontic',
            'price' => 75000,
            'duration' => 90,
            'description' => 'Tratamiento de conducto radicular',
        ],
        [
            'procedure_name' => 'Extracción',
            'diagnosis_code' => 'missing',
            'price' => 50000,
            'duration' => 30,
            'description' => 'Extracción de pieza dental',
        ],
        [
            'procedure_name' => 'Corona',
            'diagnosis_code' => 'crown',
            'price' => 90000,
            'duration' => 60,
            'description' => 'Colocación de corona dental',
        ],
        [
            'procedure_name' => 'Revisión/Control',
            'diagnosis_code' => 'healthy',
            'price' => 15000,
            'duration' => 15,
            'description' => 'Revisión general de pieza dental',
        ],
    ];

    public function run(): void
    {
        foreach ($this->diagnosisMapping as $data) {
            ProcedurePrice::firstOrCreate(
                ['clinic_id' => tenant('id'), 'diagnosis_code' => $data['diagnosis_code']],
                $data
            );
        }
    }
}
