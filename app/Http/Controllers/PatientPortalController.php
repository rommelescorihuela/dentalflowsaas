<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Patient;
use App\Services\PlanLimits;

class PatientPortalController extends Controller
{
    public function dashboard($patient)
    {
        $this->ensurePortalFeature();

        if (! ($patient instanceof Patient)) {
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

    public function viewBudget(Patient $patient, Budget $budget)
    {
        $this->ensurePortalFeature();

        if ($budget->clinic_id !== tenant('id')) {
            abort(403, 'No tienes acceso a este presupuesto.');
        }

        if ($budget->patient_id !== $patient->id) {
            abort(403, 'Este presupuesto no pertenece al paciente.');
        }

        $budget->load(['items.procedurePrice', 'patient']);

        return view('patient-portal.budget-detail', [
            'budget' => $budget,
        ]);
    }

    public function acceptBudget(Patient $patient, Budget $budget)
    {
        $this->ensurePortalFeature();

        if ($budget->clinic_id !== tenant('id')) {
            abort(403, 'No tienes acceso a este presupuesto.');
        }

        if ($budget->patient_id !== $patient->id) {
            abort(403, 'Este presupuesto no pertenece al paciente.');
        }

        $budget->update([
            'status' => 'accepted',
        ]);

        return back()->with('success', '¡Presupuesto aceptado exitosamente!');
    }

    public function rejectBudget(Patient $patient, Budget $budget)
    {
        $this->ensurePortalFeature();

        if ($budget->clinic_id !== tenant('id')) {
            abort(403, 'No tienes acceso a este presupuesto.');
        }

        if ($budget->patient_id !== $patient->id) {
            abort(403, 'Este presupuesto no pertenece al paciente.');
        }

        $budget->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Presupuesto rechazado.');
    }

    protected function ensurePortalFeature(): void
    {
        $tenant = tenant();

        if (! $tenant) {
            abort(403);
        }

        if (! app(PlanLimits::class)->hasFeature($tenant, 'portal')) {
            abort(403, 'El portal del paciente no está disponible en tu plan.');
        }
    }
}
