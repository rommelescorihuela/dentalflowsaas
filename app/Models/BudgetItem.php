<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToClinic;

class BudgetItem extends Model
{
    use BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'budget_id',
        'procedure_price_id',
        'treatment_name',
        'quantity',
        'cost',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function procedurePrice(): BelongsTo
    {
        return $this->belongsTo(ProcedurePrice::class);
    }
}
