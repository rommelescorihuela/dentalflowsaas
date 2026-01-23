<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\ActivityLogger;

class Appointment extends Model
{
    use BelongsToClinic, ActivityLogger;


    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();
        self::observe(\App\Observers\AppointmentObserver::class);
    }

    public function procedurePrice(): BelongsTo
    {
        return $this->belongsTo(ProcedurePrice::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    use BelongsToClinic, ActivityLogger;

    // ... (rest of imports at top of file, ideally)

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function treatments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Treatment::class);
    }
}
