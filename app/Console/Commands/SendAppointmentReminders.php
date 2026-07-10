<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\AppointmentReminder;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';

    protected $description = 'Envía recordatorios por email 24h antes de cada cita programada';

    public function handle(): int
    {
        $tomorrow = now()->addDay();
        $startOfDay = $tomorrow->copy()->startOfDay();
        $endOfDay = $tomorrow->copy()->endOfDay();

        $appointments = Appointment::with(['patient', 'clinic'])
            ->where('status', 'scheduled')
            ->whereBetween('start_time', [$startOfDay, $endOfDay])
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($appointments as $appointment) {
            if (! $appointment->patient?->email) {
                $skipped++;

                continue;
            }

            Mail::to($appointment->patient->email)
                ->send(new AppointmentReminder($appointment));

            $sent++;
        }

        $this->info("Recordatorios enviados: {$sent}, omitidos (sin email): {$skipped}");

        return Command::SUCCESS;
    }
}
