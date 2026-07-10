<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcedurePrice extends Model
{
    use BelongsToClinic, HasFactory;

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
