<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToClinic;
use App\Traits\ActivityLogger;

class Treatment extends Model
{
    use BelongsToClinic, ActivityLogger;

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
