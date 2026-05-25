<?php

namespace App\Filament\App\Widgets;

use App\Models\Appointment;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class CalendarWidget extends Widget
{
    protected string $view = 'filament.app.widgets.calendar-widget';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function getEvents(): array
    {
        return Appointment::query()
            ->with('patient')
            ->whereDate('start_time', '>=', now()->startOfMonth()->subMonth())
            ->whereDate('end_time', '<=', now()->endOfMonth()->addMonth())
            ->get()
            ->map(fn(Appointment $appointment) => [
                'id' => $appointment->id,
                'title' => $appointment->patient->name . ' - ' . match($appointment->type) {
                    'control' => 'Control',
                    'urgent' => 'Urgente',
                    'cleaning' => 'Limpieza',
                    'surgery' => 'Cirugía',
                    default => ucfirst($appointment->type),
                },
                'start' => $appointment->start_time->toIso8601String(),
                'end' => $appointment->end_time->toIso8601String(),
                'backgroundColor' => match ($appointment->status) {
                    'confirmed' => '#10b981', // green
                    'cancelled' => '#ef4444', // red
                    'completed' => '#3b82f6', // blue
                    default => '#6b7280', // gray
                },
                'borderColor' => 'transparent',
            ])
            ->toArray();
    }

    public function updateAppointment(int $id, string $start, string $end): void
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return;
        }

        $newStart = Carbon::parse($start);
        $newEnd = Carbon::parse($end);

        // Validate: no past dates
        if ($newStart->lt(now())) {
            \Filament\Notifications\Notification::make()
        ->title('No se puede reagendar a fecha pasada')
        ->body('Las citas no pueden moverse a una fecha en el pasado.')
                ->danger()
                ->send();
            return;
        }

        // Validate: no overlapping appointments for the same patient
        $overlapping = Appointment::where('patient_id', $appointment->patient_id)
            ->where('id', '!=', $appointment->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($newStart, $newEnd) {
                $query->where(function ($q) use ($newStart, $newEnd) {
                    $q->where('start_time', '<', $newEnd)
                      ->where('end_time', '>', $newStart);
                });
            })
            ->exists();

        if ($overlapping) {
            \Filament\Notifications\Notification::make()
        ->title('Horario no disponible')
        ->body('El paciente ya tiene una cita durante este horario.')
                ->danger()
                ->send();
            return;
        }

        $appointment->update([
            'start_time' => $newStart,
            'end_time' => $newEnd,
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Cita Reagendada')
            ->success()
            ->send();
    }
}
