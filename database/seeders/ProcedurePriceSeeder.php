<?php

namespace Database\Seeders;

use App\Models\ProcedurePrice;
use Illuminate\Database\Seeder;

class ProcedurePriceSeeder extends Seeder
{
    protected array $procedures = [
        // === DIAGNÓSTICOS BÁSICOS DEL ODONTOGRAMA (necesarios para colores) ===
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

        // === ODONTOLOGÍA GENERAL ===
        [
            'procedure_name' => 'Limpieza Dental (Profilaxis)',
            'diagnosis_code' => 'prophylaxis',
            'price' => 35000,
            'duration' => 45,
            'description' => 'Limpieza profesional y eliminación de sarro',
        ],
        [
            'procedure_name' => 'Sellante de Fosas y Fisuras',
            'diagnosis_code' => 'sealant',
            'price' => 25000,
            'duration' => 20,
            'description' => 'Sellante preventivo en superficies oclusales',
        ],
        [
            'procedure_name' => 'Fluorización',
            'diagnosis_code' => 'fluoride',
            'price' => 15000,
            'duration' => 15,
            'description' => 'Aplicación de flúor para fortalecimiento dental',
        ],
        [
            'procedure_name' => 'Incrustación (Inlay/Onlay)',
            'diagnosis_code' => 'inlay',
            'price' => 85000,
            'duration' => 60,
            'description' => 'Restauración indirecta en cerámica o composite',
        ],

        // === ENDODONCIA ===
        [
            'procedure_name' => 'Endodoncia Unirradicular',
            'diagnosis_code' => 'endodontic',
            'price' => 75000,
            'duration' => 90,
            'description' => 'Tratamiento de conducto en diente de una raíz',
        ],
        [
            'procedure_name' => 'Endodoncia Multirradicular',
            'diagnosis_code' => 'endodontic_multi',
            'price' => 100000,
            'duration' => 120,
            'description' => 'Tratamiento de conducto en muelas de múltiples raíces',
        ],
        [
            'procedure_name' => 'Retratamiento Endodóntico',
            'diagnosis_code' => 'endo_retreatment',
            'price' => 120000,
            'duration' => 120,
            'description' => 'Retratamiento de conducto previamente realizado',
        ],

        // === PERIODONCIA ===
        [
            'procedure_name' => 'Raspado y Alisado Radicular',
            'diagnosis_code' => 'scaling',
            'price' => 60000,
            'duration' => 60,
            'description' => 'Tratamiento periodontal no quirúrgico',
        ],
        [
            'procedure_name' => 'Gingivectomía',
            'diagnosis_code' => 'gingivectomy',
            'price' => 80000,
            'duration' => 60,
            'description' => 'Esculpido gingival por exceso de encía',
        ],
        [
            'procedure_name' => 'Colgajo Periodontal',
            'diagnosis_code' => 'flap_surgery',
            'price' => 150000,
            'duration' => 90,
            'description' => 'Cirugía periodontal para acceso a raíz',
        ],

        // === CIRUGÍA ORAL ===
        [
            'procedure_name' => 'Extracción Simple',
            'diagnosis_code' => 'missing',
            'price' => 50000,
            'duration' => 30,
            'description' => 'Extracción de pieza dental erupcionada',
        ],
        [
            'procedure_name' => 'Extracción Quirúrgica',
            'diagnosis_code' => 'surgical_extraction',
            'price' => 80000,
            'duration' => 60,
            'description' => 'Extracción de pieza retenida o impactada',
        ],
        [
            'procedure_name' => 'Extracción de Cordal (Cordal)',
            'diagnosis_code' => 'wisdom_tooth',
            'price' => 100000,
            'duration' => 90,
            'description' => 'Extracción de tercer molar impactado',
        ],
        [
            'procedure_name' => 'Apicectomía',
            'diagnosis_code' => 'apicoectomy',
            'price' => 120000,
            'duration' => 90,
            'description' => 'Cirugía de resección del ápice radicular',
        ],
        [
            'procedure_name' => 'Frenectomía',
            'diagnosis_code' => 'frenectomy',
            'price' => 70000,
            'duration' => 45,
            'description' => 'Eliminación de frenillo labial o lingual',
        ],

        // === IMPLANTOLOGÍA ===
        [
            'procedure_name' => 'Implante Dental',
            'diagnosis_code' => 'implant',
            'price' => 250000,
            'duration' => 60,
            'description' => 'Colocación de implante endoóseo',
        ],
        [
            'procedure_name' => 'Corona sobre Implante',
            'diagnosis_code' => 'implant_crown',
            'price' => 120000,
            'duration' => 60,
            'description' => 'Prótesis fija sobre implante',
        ],
        [
            'procedure_name' => 'Elevación de Seno Maxilar',
            'diagnosis_code' => 'sinus_lift',
            'price' => 300000,
            'duration' => 120,
            'description' => 'Aumento de hueso en maxilar superior',
        ],

        // === ORTODONCIA ===
        [
            'procedure_name' => 'Bracket Metálico (por arco)',
            'diagnosis_code' => 'braces_metal',
            'price' => 150000,
            'duration' => 60,
            'description' => 'Instalación de brackets metálicos por arco',
        ],
        [
            'procedure_name' => 'Bracket Estético (por arco)',
            'diagnosis_code' => 'braces_aesthetic',
            'price' => 200000,
            'duration' => 60,
            'description' => 'Instalación de brackets estéticos/cerámicos',
        ],
        [
            'procedure_name' => 'Ajuste Ortodóntico',
            'diagnosis_code' => 'ortho_adjustment',
            'price' => 30000,
            'duration' => 30,
            'description' => 'Control y ajuste de brackets',
        ],
        [
            'procedure_name' => 'Contención Fija',
            'diagnosis_code' => 'retainer_fixed',
            'price' => 80000,
            'duration' => 45,
            'description' => 'Colocación de retenedor fijo post-ortodoncia',
        ],
        [
            'procedure_name' => 'Contención Removible',
            'diagnosis_code' => 'retainer_removable',
            'price' => 60000,
            'duration' => 30,
            'description' => 'Fabricación de retenedor removible',
        ],

        // === PRÓTESIS ===
        [
            'procedure_name' => 'Corona Metálica',
            'diagnosis_code' => 'crown',
            'price' => 90000,
            'duration' => 60,
            'description' => 'Corona completa de metal',
        ],
        [
            'procedure_name' => 'Corona Cerámica/Metal',
            'diagnosis_code' => 'crown_pfm',
            'price' => 120000,
            'duration' => 60,
            'description' => 'Corona de porcelana sobre metal',
        ],
        [
            'procedure_name' => 'Corona Zirconio',
            'diagnosis_code' => 'crown_zirconia',
            'price' => 180000,
            'duration' => 60,
            'description' => 'Corona de zirconio de alta estética',
        ],
        [
            'procedure_name' => 'Puente (3 unidades)',
            'diagnosis_code' => 'bridge',
            'price' => 250000,
            'duration' => 90,
            'description' => 'Puente fijo de 3 unidades',
        ],
        [
            'procedure_name' => 'Prótesis Parcial Removible',
            'diagnosis_code' => 'partial_denture',
            'price' => 150000,
            'duration' => 60,
            'description' => 'Prótesis parcial acrílica o metálica',
        ],
        [
            'procedure_name' => 'Prótesis Total',
            'diagnosis_code' => 'full_denture',
            'price' => 250000,
            'duration' => 90,
            'description' => 'Prótesis completa (maxilar o mandibular)',
        ],
        [
            'procedure_name' => 'Rebase de Prótesis',
            'diagnosis_code' => 'denture_rebase',
            'price' => 60000,
            'duration' => 45,
            'description' => 'Renovación de base de prótesis',
        ],

        // === ESTÉTICA DENTAL ===
        [
            'procedure_name' => 'Blanqueamiento Dental',
            'diagnosis_code' => 'whitening',
            'price' => 120000,
            'duration' => 60,
            'description' => 'Blanqueamiento con gel y luz LED',
        ],
        [
            'procedure_name' => 'Carilla Composite',
            'diagnosis_code' => 'veneer_composite',
            'price' => 80000,
            'duration' => 60,
            'description' => 'Carilla directa en resina composite',
        ],
        [
            'procedure_name' => 'Carilla Cerámica',
            'diagnosis_code' => 'veneer_ceramic',
            'price' => 150000,
            'duration' => 60,
            'description' => 'Carilla indirecta en cerámica',
        ],
        [
            'procedure_name' => 'Gingivectomía Estética',
            'diagnosis_code' => 'gingival_contouring',
            'price' => 100000,
            'duration' => 60,
            'description' => 'Esculpido gingival para mejorar sonrisa',
        ],

        // === PEDIATRÍA ===
        [
            'procedure_name' => 'Corona de Acero (Pediatría)',
            'diagnosis_code' => 'ss_crown',
            'price' => 60000,
            'duration' => 45,
            'description' => 'Corona de acero inoxidable para diente temporal',
        ],
        [
            'procedure_name' => 'Pulpotomía',
            'diagnosis_code' => 'pulpotomy',
            'price' => 45000,
            'duration' => 45,
            'description' => 'Tratamiento de pulpa en diente temporal',
        ],
        [
            'procedure_name' => 'Mantenedor de Espacio',
            'diagnosis_code' => 'space_maintainer',
            'price' => 70000,
            'duration' => 45,
            'description' => 'Aparato para mantener espacio tras pérdida prematura',
        ],

        // === DIAGNÓSTICO Y RADIOLOGÍA ===
        [
            'procedure_name' => 'Consulta General',
            'diagnosis_code' => 'consultation',
            'price' => 20000,
            'duration' => 30,
            'description' => 'Evaluación clínica general',
        ],
        [
            'procedure_name' => 'Radiografía Periapical',
            'diagnosis_code' => 'xray_periapical',
            'price' => 10000,
            'duration' => 10,
            'description' => 'Radiografía de pieza dental individual',
        ],
        [
            'procedure_name' => 'Radiografía Panorámica',
            'diagnosis_code' => 'xray_panoramic',
            'price' => 25000,
            'duration' => 15,
            'description' => 'Radiografía panorámica completa',
        ],
        [
            'procedure_name' => 'Cone Beam (CBCT)',
            'diagnosis_code' => 'cbct',
            'price' => 80000,
            'duration' => 20,
            'description' => 'Tomografía computarizada de cono',
        ],
    ];

    public function run(): void
    {
        $clinicId = tenant('id') ?? \App\Models\Clinic::first()?->id;

        if (!$clinicId) {
            $this->command->error('No se encontró un tenant activo. Ejecuta este seeder dentro de un contexto de tenancy o asegúrate de tener al menos un tenant creado.');
            return;
        }

        foreach ($this->procedures as $data) {
            ProcedurePrice::firstOrCreate(
                [
                    'clinic_id' => $clinicId,
                    'procedure_name' => $data['procedure_name'],
                ],
                $data
            );
        }

        $this->command->info('✅ ' . count($this->procedures) . ' procedimientos sembrados para el tenant: ' . $clinicId);
    }
}
