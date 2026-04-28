<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToClinic;

class ProcedurePrice extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'procedure_name',
        'diagnosis_code',
        'price',
        'duration',
        'image_path',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function procedureInventories(): HasMany
    {
        return $this->hasMany(ProcedureInventory::class);
    }
}
