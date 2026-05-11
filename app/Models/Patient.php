<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\BelongsToClinic;
use App\Traits\ActivityLogger;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory, BelongsToClinic, ActivityLogger;

    protected $fillable = [
        'clinic_id',
        'name',
        'email',
        'phone',
        'rut',
        'birth_date',
        'medical_history',
        'allergies',
    ];

    protected $casts = [
        'medical_history' => 'array',
        'allergies' => 'array',
    ];

    public function odontograms(): HasMany
    {
        return $this->hasMany(Odontogram::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function clinicalRecords(): HasMany
    {
        return $this->hasMany(ClinicalRecord::class);
    }
}