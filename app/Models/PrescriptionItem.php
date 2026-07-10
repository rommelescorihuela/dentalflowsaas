<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionItem extends Model
{
    use BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'prescription_id',
        'medication',
        'dosage',
        'frequency',
        'duration',
        'quantity',
        'indications',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }
}
