<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcedureInventory extends Model
{
    use HasFactory, BelongsToClinic;

    protected $table = 'procedure_inventory';

    protected $guarded = [];

    protected $casts = [
        'quantity_used' => 'decimal:2',
    ];

    public function procedurePrice(): BelongsTo
    {
        return $this->belongsTo(ProcedurePrice::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
