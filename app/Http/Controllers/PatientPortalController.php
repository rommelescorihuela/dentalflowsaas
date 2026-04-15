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
            $patient = Patient::findOrFail($patient);
        }
        $patient->load(['appointments', 'budgets', 'clinicalRecords']);

        return view('patient-portal.dashboard', [
            'patient' => $patient,
        ]);
    }

    public function acceptBudget(Budget $budget)
    {
        $budget->update([
            'status' => 'accepted',
        ]);

        return back()->with('success', 'Budget accepted successfully!');
    }
}
