<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;

class Inventory extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'name',
        'price',
        'expiration_date',
        'quantity',
        'low_stock_threshold',
        'unit',
        'items_per_unit',
        'supplier',
        'expiration_type',
        'category',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'items_per_unit' => 'integer',
    ];

}
