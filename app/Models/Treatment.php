<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToClinic;
use App\Traits\ActivityLogger;

class Treatment extends Model
{
    use BelongsToClinic, ActivityLogger;

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
