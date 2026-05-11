<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\ActivityLogger;

class Payment extends Model
{
    use HasFactory, BelongsToClinic, ActivityLogger;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'appointment_id',
        'budget_id',
        'amount',
        'method',
        'status',
        'reference_id',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }
}
