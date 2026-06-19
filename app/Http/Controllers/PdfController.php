<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Odontogram;
use App\Services\PdfService;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function __construct(
        protected PdfService $pdfService
    ) {}

    public function downloadBudget(Request $request, Budget $budget)
    {
        if ($budget->clinic_id !== tenant('id')) {
            abort(403);
        }

        return $this->pdfService->generateBudgetPdf($budget);
    }

    public function downloadOdontogram(Request $request, Odontogram $odontogram)
    {
        if ($odontogram->clinic_id !== tenant('id')) {
            abort(403);
        }

        return $this->pdfService->generateOdontogramPdf($odontogram);
    }

    public function downloadBudgetPortal(Request $request, Budget $budget)
    {
        if ($budget->clinic_id !== tenant('id')) {
            abort(403);
        }

        return $this->pdfService->generateBudgetPdf($budget);
    }
}
