<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\ActivityLogger;

class SubscriptionPayment extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory, ActivityLogger;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
