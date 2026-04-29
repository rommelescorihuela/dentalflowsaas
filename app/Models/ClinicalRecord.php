<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalRecord extends Model
{
    use BelongsToClinic;

    protected $guarded = [];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function odontogram(): BelongsTo
    {
        return $this->belongsTo(Odontogram::class);
    }

    public function procedurePrice(): BelongsTo
    {
        return $this->belongsTo(ProcedurePrice::class);
    }
}
