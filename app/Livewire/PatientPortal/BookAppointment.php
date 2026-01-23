<?php

namespace App\Livewire\PatientPortal;

use Livewire\Component;
use App\Models\Patient;
use App\Models\ProcedurePrice;
use App\Models\Appointment;
use Carbon\Carbon;

class BookAppointment extends Component
{
    public Patient $patient;

    // Steps
    public int $step = 1;

    // Selections
    public $selectedProcedureId;
    public $selectedDate;
    public $selectedTimeSlot;

    // Data
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
        $this->loadTimeSlots();
    }

    public function loadTimeSlots()
    {
        if (!$this->selectedDate)
            return;

        $date = Carbon::parse($this->selectedDate);

        $tenantData = tenant()->data ?? [];
        $startHour = isset($tenantData['schedule_start']) ? Carbon::parse($tenantData['schedule_start'])->hour : 9;
        $endHour = isset($tenantData['schedule_end']) ? Carbon::parse($tenantData['schedule_end'])->hour : 18;

        $start = $date->copy()->setTime($startHour, 0);
        $end = $date->copy()->setTime($endHour, 0);
        $interval = 30; // minutes

        $existingAppointments = Appointment::whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
            ->get(['start_time', 'end_time']);

        $slots = [];
        $current = $start->copy();

        while ($current->lt($end)) {
            $slotEnd = $current->copy()->addMinutes($interval);

            // Check formatted strings for simplicity in this MVP
            // Ideally, checking overlaps is better
            $isTaken = $existingAppointments->contains(function ($apt) use ($current, $slotEnd) {
                // If appointment overlaps with this slot
                return $apt->start_time->lt($slotEnd) && $apt->end_time->gt($current);
            });

            if (!$isTaken && $current->gt(now())) {
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

        $date = Carbon::parse($this->selectedDate);
        $timeParts = explode(':', $this->selectedTimeSlot);
        $startTime = $date->copy()->setTime($timeParts[0], $timeParts[1]);
        $endTime = $startTime->copy()->addMinutes(30); // Default duration

        // Create appointment
        Appointment::create([
            'patient_id' => $this->patient->id,
            'clinic_id' => tenant('id'), // Explicitly set tenant if needed, though trait handles it
            'title' => 'Cita Web: ' . ProcedurePrice::find($this->selectedProcedureId)->name,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'pending', // Pending confirmation
            'procedure_price_id' => $this->selectedProcedureId,
            // 'user_id' => null, // Assigned later by admin
        ]);

        return redirect()->route('portal.dashboard', $this->patient)
            ->with('success', '¡Cita reservada con éxito! Espera nuestra confirmación.');
    }

    public function render()
    {
        return view('livewire.patient-portal.book-appointment', [
            'procedures' => ProcedurePrice::all(),
        ]);
    }
}
