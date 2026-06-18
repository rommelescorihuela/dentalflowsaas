<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    use ActivityLogger, \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'clinic_id',
        'subscription_id',
        'amount',
        'currency',
        'method',
        'status',
        'transaction_id',
        'period_start',
        'period_end',
        'reference',
        'proof_path',
        'verified_by',
        'verified_at',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
