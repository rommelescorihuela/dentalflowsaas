<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\ActivityLogger;

class SubscriptionPayment extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory, ActivityLogger;

    protected $fillable = [
        'clinic_id',
        'amount',
        'currency',
        'method',
        'status',
        'transaction_id',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
