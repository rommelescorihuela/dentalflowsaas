<?php

declare(strict_types=1);

namespace App\Models;

use App\Mail\BudgetSent;
use App\Traits\ActivityLogger;
use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;

class Budget extends Model
{
    use ActivityLogger, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'odontogram_id',
        'user_id',
        'total',
        'status',
        'notes',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::updated(function ($budget) {
            if ($budget->isDirty('status') && $budget->status === 'sent' && $budget->patient?->email) {
                Mail::to($budget->patient->email)->send(new BudgetSent($budget));
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function odontogram(): BelongsTo
    {
        return $this->belongsTo(Odontogram::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
