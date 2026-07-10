<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\ClinicHelper;
use App\Models\Budget;
use App\Models\Odontogram;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class PdfService
{
    protected array $diagnosisColors = [
        'caries' => '#ef4444',
        'filled' => '#0891b2',
        'endodontic' => '#eab308',
        'missing' => '#1f2937',
        'healthy' => '#ffffff',
        'crown' => '#a855f7',
        'prophylaxis' => '#22c55e',
        'sealant' => '#14b8a6',
        'fluoride' => '#0891b2',
        'inlay' => '#6366f1',
        'scaling' => '#84cc16',
        'gingivectomy' => '#f97316',
        'flap_surgery' => '#dc2626',
        'surgical_extraction' => '#1f2937',
        'wisdom_tooth' => '#1f2937',
        'implant' => '#6b7280',
        'implant_crown' => '#9ca3af',
        'braces_metal' => '#a78bfa',
        'braces_aesthetic' => '#c4b5fd',
        'retainer_fixed' => '#8b5cf6',
        'retainer_removable' => '#7c3aed',
        'crown_pfm' => '#a855f7',
        'crown_zirconia' => '#9333ea',
        'bridge' => '#7e22ce',
        'partial_denture' => '#6b21a8',
        'full_denture' => '#581c87',
        'whitening' => '#f0f9ff',
        'veneer_composite' => '#bae6fd',
        'veneer_ceramic' => '#7dd3fc',
        'consultation' => '#e2e8f0',
        'xray_periapical' => '#cbd5e1',
        'xray_panoramic' => '#94a3b8',
        'cbct' => '#64748b',
    ];

    public function generateBudgetPdf(Budget $budget): Response
    {
        $budget->load(['patient', 'items.procedurePrice', 'clinic']);

        $currencySymbol = ClinicHelper::getCurrencySymbol();
        $logoPath = ClinicHelper::getLogo();
        $logo = $logoPath ? public_path('storage/'.$logoPath) : null;

        $pdf = Pdf::loadView('pdf.budget', [
            'budget' => $budget,
            'clinic' => $budget->clinic,
            'currencySymbol' => $currencySymbol,
            'logo' => $logo,
        ]);

        $filename = "presupuesto-{$budget->id}-{$budget->patient->name}.pdf";

        return $pdf->download($filename);
    }

    public function generateOdontogramPdf(Odontogram $odontogram): Response
    {
        $odontogram->load(['patient', 'clinicalRecords', 'clinic']);

        $logoPath = ClinicHelper::getLogo();
        $logo = $logoPath ? public_path('storage/'.$logoPath) : null;

        $toothMap = [];
        foreach ($odontogram->clinicalRecords as $record) {
            if (! isset($toothMap[$record->tooth_number])) {
                $toothMap[$record->tooth_number] = [];
            }
            $toothMap[$record->tooth_number][$record->surface] = $record->diagnosis_code;
        }

        $pdf = Pdf::loadView('pdf.odontogram', [
            'odontogram' => $odontogram,
            'patient' => $odontogram->patient,
            'clinic' => $odontogram->clinic,
            'toothMap' => $toothMap,
            'colors' => $this->diagnosisColors,
            'logo' => $logo,
            'upperRight' => [18, 17, 16, 15, 14, 13, 12, 11],
            'upperLeft' => [21, 22, 23, 24, 25, 26, 27, 28],
            'lowerRight' => [48, 47, 46, 45, 44, 43, 42, 41],
            'lowerLeft' => [31, 32, 33, 34, 35, 36, 37, 38],
        ]);

        $filename = "odontograma-{$odontogram->id}-{$odontogram->patient->name}.pdf";

        return $pdf->download($filename);
    }

    public function getDiagnosisColors(): array
    {
        return $this->diagnosisColors;
    }
}
