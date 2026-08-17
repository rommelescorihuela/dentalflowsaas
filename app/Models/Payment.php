<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToClinic;
use App\Traits\LogsTenantActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use BelongsToClinic, HasFactory, LogsTenantActivity;

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
