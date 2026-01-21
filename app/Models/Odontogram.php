<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToClinic;

class Odontogram extends Model
{
    use BelongsToClinic;

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
}
