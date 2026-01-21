<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ProcedurePrice extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'procedure_name',
        'price',
        'duration',
        'image_path',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function clinic()
    {
        return $this->tenant();
    }
}
