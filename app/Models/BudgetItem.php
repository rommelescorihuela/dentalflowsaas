<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToClinic;

class BudgetItem extends Model
{
    use BelongsToClinic;

    protected $guarded = [];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }
}
