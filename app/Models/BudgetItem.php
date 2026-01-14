<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetItem extends Model
{
    protected $guarded = [];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }
}
