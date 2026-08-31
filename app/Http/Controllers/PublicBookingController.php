<?php

namespace App\Http\Controllers;

use App\Helpers\ClinicHelper;
use App\Mail\PortalWelcome;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\ProcedurePrice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class PublicBookingController extends Controller
{
    public function show(string $clinicId)
    {
        $clinic = Clinic::findOrFail($clinicId);

        $setting = ClinicSetting::where('clinic_id', $clinicId)->first();

        if (! $setting || ! $setting->landing_enabled) {
            abort(404);
        }

        $this->initializeTenancy($clinic);

        $procedures = ProcedurePrice::where('clinic_id', $clinicId)->get();

        return view('landing.book', [
            'clinic' => $clinic,
            'setting' => $setting,
            'procedures' => $procedures,
        ]);
    }

    public function slots(Request $request, string $clinicId)
    {
        $clinic = Clinic::findOrFail($clinicId);

        $this->initializeTenancy($clinic);

        $date = $request->query('date');
        $procedureId = $request->query('procedure_id');

        if (! $date) {
            return response()->json(['slots' => []]);
        }

        $date = Carbon::parse($date);

        $startHour = 9;
        $endHour = 18;

        $scheduleStart = ClinicHelper::getScheduleStart();
        $scheduleEnd = ClinicHelper::getScheduleEnd();

        if ($scheduleStart) {
            $startHour = Carbon::parse($scheduleStart)->hour;
        }
        if ($scheduleEnd) {
            $endHour = Carbon::parse($scheduleEnd)->hour;
        }

        $interval = 30;
        if ($procedureId) {
            $procedure = ProcedurePrice::find($procedureId);
            if ($procedure && $procedure->duration) {
                $interval = (int) $procedure->duration;
            }
        }

        $start = $date->copy()->setTime($startHour, 0);
        $end = $date->copy()->setTime($endHour, 0);

        $existingAppointments = Appointment::whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
            ->get(['start_time', 'end_time']);

        $slots = [];
        $current = $start->copy();

        while ($current->lt($end)) {
            $slotEnd = $current->copy()->addMinutes($interval);

            $isTaken = $existingAppointments->contains(function ($apt) use ($current, $slotEnd) {
                return $apt->start_time->lt($slotEnd) && $apt->end_time->gt($current);
            });

            if (! $isTaken && $current->gt(now())) {
                $slots[] = $current->format('H:i');
            }

            $current->addMinutes($interval);
        }

        return response()->json(['slots' => $slots]);
    }

    public function store(Request $request, string $clinicId)
    {
        $clinic = Clinic::findOrFail($clinicId);

        $this->initializeTenancy($clinic);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'procedure_price_id' => 'required|exists:procedure_prices,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ]);

        $procedure = ProcedurePrice::where('clinic_id', $clinicId)
            ->findOrFail($validated['procedure_price_id']);

        $patient = Patient::where('clinic_id', $clinicId)
            ->where('email', $validated['email'])
            ->first();

        if (! $patient) {
            $patient = Patient::create([
                'clinic_id' => $clinicId,
                'status' => 'active',
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);
        } else {
            $patient->update([
                'status' => 'active',
                'name' => $validated['name'],
                'phone' => $validated['phone'],
            ]);
        }

        $duration = (int) ($procedure->duration ?? 30);

        $date = Carbon::parse($validated['date']);
        $timeParts = explode(':', $validated['time']);
        $startTime = $date->copy()->setTime((int) $timeParts[0], (int) $timeParts[1]);
        $endTime = $startTime->copy()->addMinutes($duration);

        try {
            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'clinic_id' => $clinicId,
                'procedure_price_id' => $procedure->id,
                'notes' => $validated['notes'] ? 'Cita Web: '.$procedure->procedure_name."\n".$validated['notes'] : 'Cita Web: '.$procedure->procedure_name,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'scheduled',
                'type' => 'control',
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $portalUrl = URL::signedRoute('portal.dashboard', [
            'tenant' => $clinicId,
            'patient' => $patient,
        ]);

        Mail::to($patient->email)->send(new PortalWelcome($patient, $portalUrl));

        return view('landing.booking-confirmation', [
            'clinic' => $clinic,
            'setting' => ClinicSetting::where('clinic_id', $clinicId)->first(),
            'appointment' => $appointment,
            'procedure' => $procedure,
        ]);
    }

    protected function initializeTenancy(Clinic $clinic): void
    {
        if (! tenancy()->initialized) {
            tenancy()->initialize($clinic);
        }
    }
}
