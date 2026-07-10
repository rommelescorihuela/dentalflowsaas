<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\AppointmentObserver;
use App\Traits\ActivityLogger;
use App\Traits\BelongsToClinic;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Appointment extends Model
{
    use ActivityLogger, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'user_id',
        'procedure_price_id',
        'start_time',
        'end_time',
        'status',
        'type',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public static function boot(): void
    {
        parent::boot();
        self::observe(AppointmentObserver::class);

        static::creating(function ($appointment) {
            if ($appointment->start_time && Carbon::parse($appointment->start_time)->isPast()) {
                throw ValidationException::withMessages([
                    'start_time' => ['No se pueden crear citas en el pasado.'],
                ]);
            }

            if ($appointment->start_time && $appointment->patient_id) {
                $overlapping = static::where('patient_id', $appointment->patient_id)
                    ->where('clinic_id', $appointment->clinic_id)
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($query) use ($appointment) {
                        $query->whereBetween('start_time', [$appointment->start_time, $appointment->end_time])
                            ->orWhereBetween('end_time', [$appointment->start_time, $appointment->end_time])
                            ->orWhere(function ($q) use ($appointment) {
                                $q->where('start_time', '<=', $appointment->start_time)
                                    ->where('end_time', '>=', $appointment->end_time);
                            });
                    })
                    ->exists();

                if ($overlapping) {
                    throw ValidationException::withMessages([
                        'start_time' => ['El paciente ya tiene una cita agendada en ese horario.'],
                    ]);
                }
            }
        });
    }

    public function procedurePrice(): BelongsTo
    {
        return $this->belongsTo(ProcedurePrice::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class);
    }
}
