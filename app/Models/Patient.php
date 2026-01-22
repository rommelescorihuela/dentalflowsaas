<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\BelongsToClinic;

use App\Traits\ActivityLogger;

class Patient extends Model
{
    use HasFactory, BelongsToClinic, ActivityLogger;


    protected $guarded = [];

    protected $casts = [
        'medical_history' => 'array',
        'allergies' => 'array',
    ];

    public function odontograms()
    {
        return $this->hasMany(Odontogram::class);
    }
}
