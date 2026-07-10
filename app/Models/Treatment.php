<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ActivityLogger;
use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    use ActivityLogger, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'appointment_id',
        'name',
        'description',
        'price',
        'code',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
