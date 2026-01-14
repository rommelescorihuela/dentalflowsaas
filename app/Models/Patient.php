<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Patient extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'medical_history' => 'array',
        'allergies' => 'array',
    ];

    public function clinic()
    {
        return $this->tenant();
    }

    public function odontograms()
    {
        return $this->hasMany(Odontogram::class);
    }
}
