<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ActivityLogger;
use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Odontogram extends Model
{
    use ActivityLogger, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'name',
        'date',
        'notes',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function clinicalRecords(): HasMany
    {
        return $this->hasMany(ClinicalRecord::class);
    }

    public function budget(): HasOne
    {
        return $this->hasOne(Budget::class);
    }
}
