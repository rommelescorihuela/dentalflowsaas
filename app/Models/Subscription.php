<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Plan;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'clinic_id',
        'plan',
        'status',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'cancelled_at',
        'seats_limit',
        'patients_limit',
    ];

    protected $casts = [
        'plan' => Plan::class,
        'status' => SubscriptionStatus::class,
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'date',
        'current_period_end' => 'date',
        'cancelled_at' => 'datetime',
        'seats_limit' => 'integer',
        'patients_limit' => 'integer',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function hasAccess(): bool
    {
        return $this->status->hasAccess();
    }

    public function effectivePlan(): Plan
    {
        // During trial, the clinic gets Pro access (all features, no limits).
        // This matches config('plans.free_trial') which mirrors Pro.
        // To restrict trial access, just change the free_trial config.
        if ($this->status === SubscriptionStatus::Trialing) {
            return Plan::Pro;
        }

        return $this->plan;
    }
}
