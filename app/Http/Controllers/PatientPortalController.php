<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Patient;
use App\Models\Budget;


class PatientPortalController extends Controller
{
    public function dashboard($patient)
    {
        if (!($patient instanceof Patient)) {
            $patient = Patient::where('id', $patient)
                ->where('clinic_id', tenant('id'))
                ->firstOrFail();
        }

        if ($patient->clinic_id !== tenant('id')) {
            abort(403, 'No tienes acceso a este paciente.');
        }

        $patient->load(['appointments', 'budgets', 'clinicalRecords']);

        return view('patient-portal.dashboard', [
            'patient' => $patient,
        ]);
    }

    public function acceptBudget(Budget $budget)
    {
        if ($budget->clinic_id !== tenant('id')) {
            abort(403, 'No tienes acceso a este presupuesto.');
        }

        $budget->update([
            'status' => 'accepted',
        ]);

        return back()->with('success', 'Budget accepted successfully!');
    }
}
