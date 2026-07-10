<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
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

    public function appointments(Patient $patient)
    {
        $this->ensurePortalFeature();

        if ($patient->clinic_id !== tenant('id')) {
            abort(403, 'No tienes acceso a este paciente.');
        }

        $upcomingAppointments = $patient->appointments()
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->get();

        $pastAppointments = $patient->appointments()
            ->where('start_time', '<', now())
            ->orderBy('start_time', 'desc')
            ->get();

        return view('patient-portal.appointments', [
            'patient' => $patient,
            'upcomingAppointments' => $upcomingAppointments,
            'pastAppointments' => $pastAppointments,
        ]);
    }

    public function cancelAppointment(Patient $patient, Appointment $appointment)
    {
        $this->ensurePortalFeature();

        if ($patient->clinic_id !== tenant('id')) {
            abort(403, 'No tienes acceso a este paciente.');
        }

        if ($appointment->patient_id !== $patient->id) {
            abort(403, 'Esta cita no pertenece al paciente.');
        }

        if (! in_array($appointment->status, ['scheduled', 'confirmed'])) {
            return back()->with('error', 'Esta cita no puede ser cancelada.');
        }

        $appointment->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Cita cancelada exitosamente.');
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

    public function prescriptions(Patient $patient)
    {
        $this->ensurePortalFeature();

        if ($patient->clinic_id !== tenant('id')) {
            abort(403, 'No tienes acceso a este paciente.');
        }

        $prescriptions = $patient->prescriptions()
            ->with(['items', 'doctor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('patient-portal.prescriptions', [
            'patient' => $patient,
            'prescriptions' => $prescriptions,
        ]);
    }

    public function medicalHistory(Patient $patient)
    {
        $this->ensurePortalFeature();

        if ($patient->clinic_id !== tenant('id')) {
            abort(403, 'No tienes acceso a este paciente.');
        }

        $medicalHistory = $patient->medicalHistory;

        return view('patient-portal.medical-history', [
            'patient' => $patient,
            'medicalHistory' => $medicalHistory,
        ]);
    }

    public function payments(Patient $patient)
    {
        $this->ensurePortalFeature();

        if ($patient->clinic_id !== tenant('id')) {
            abort(403, 'No tienes acceso a este paciente.');
        }

        $payments = $patient->payments()
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPaid = $payments->where('status', 'completed')->sum('amount');
        $totalBudgeted = $patient->budgets()
            ->whereIn('status', ['accepted', 'sent'])
            ->sum('total');

        return view('patient-portal.payments', [
            'patient' => $patient,
            'payments' => $payments,
            'totalPaid' => $totalPaid,
            'totalBudgeted' => $totalBudgeted,
        ]);
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
