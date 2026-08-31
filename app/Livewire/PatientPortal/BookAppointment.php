<?php

namespace App\Livewire\PatientPortal;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\ProcedurePrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Livewire\Component;

class BookAppointment extends Component
{
    public Patient $patient;

    public int $step = 1;

    public $selectedProcedureId;

    public $selectedDate;

    public $selectedTimeSlot;

    public $availableSlots = [];

    protected $rules = [
        'selectedProcedureId' => 'required',
        'selectedDate' => 'required|date|after_or_equal:today',
        'selectedTimeSlot' => 'required',
    ];

    public function mount(Patient $patient)
    {
        $this->patient = $patient;
    }

    public function updatedSelectedDate($value)
    {
        $this->selectedTimeSlot = null;
        $this->loadTimeSlots();
    }

    public function updatedSelectedProcedureId()
    {
        $this->selectedTimeSlot = null;
        if ($this->selectedDate) {
            $this->loadTimeSlots();
        }
    }

    public function loadTimeSlots()
    {
        if (! $this->selectedDate) {
            return;
        }

        $date = Carbon::parse($this->selectedDate);

        $tenantData = tenant()->data ?? [];
        $startHour = isset($tenantData['schedule_start']) ? Carbon::parse($tenantData['schedule_start'])->hour : 9;
        $endHour = isset($tenantData['schedule_end']) ? Carbon::parse($tenantData['schedule_end'])->hour : 18;

        $interval = 30;
        if ($this->selectedProcedureId) {
            $procedure = ProcedurePrice::find($this->selectedProcedureId);
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

        $this->availableSlots = $slots;
    }

    public function nextStep()
    {
        $this->validate([
            'selectedProcedureId' => $this->step === 1 ? 'required' : '',
            'selectedDate' => $this->step === 2 ? 'required' : '',
        ]);

        if ($this->step === 2) {
            $this->loadTimeSlots();
        }

        $this->step++;
    }

    public function previousStep()
    {
        $this->step--;
    }

    public function book()
    {
        $this->validate();

        $procedure = ProcedurePrice::findOrFail($this->selectedProcedureId);
        $duration = (int) ($procedure->duration ?? 30);

        $date = Carbon::parse($this->selectedDate);
        $timeParts = explode(':', $this->selectedTimeSlot);
        $startTime = $date->copy()->setTime($timeParts[0], $timeParts[1]);
        $endTime = $startTime->copy()->addMinutes($duration);

        Appointment::create([
            'patient_id' => $this->patient->id,
            'clinic_id' => tenant('id'),
            'notes' => 'Cita Web: '.$procedure->procedure_name,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'scheduled',
            'type' => 'control',
            'procedure_price_id' => $this->selectedProcedureId,
        ]);

        $this->patient->activate();

        return redirect()->to(URL::signedRoute('portal.dashboard', ['patient' => $this->patient]))
            ->with('success', '¡Cita reservada con éxito! Espera nuestra confirmación.');
    }

    public function render()
    {
        return view('livewire.patient-portal.book-appointment', [
            'procedures' => ProcedurePrice::where('clinic_id', tenant('id'))->get(),
        ]);
    }
}
