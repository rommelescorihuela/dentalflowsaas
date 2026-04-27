<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

use App\Traits\ActivityLogger;

class Appointment extends Model
{
    use BelongsToClinic, ActivityLogger;


    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();
        self::observe(\App\Observers\AppointmentObserver::class);

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
        return $this->belongsTo(User::class , 'user_id');
    }

    public function treatments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Treatment::class);
    }
}