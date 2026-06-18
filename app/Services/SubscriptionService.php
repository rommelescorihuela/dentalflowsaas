<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Plan;
use App\Enums\SubscriptionStatus;
use App\Models\Clinic;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function createTrial(Clinic $clinic): Subscription
    {
        return Subscription::create([
            'clinic_id' => $clinic->id,
            'plan' => Plan::FreeTrial->value,
            'status' => SubscriptionStatus::Trialing->value,
            'trial_ends_at' => now()->addDays(Plan::FreeTrial->trialDays()),
        ]);
    }

    public function activate(Clinic $clinic, Plan $plan, SubscriptionPayment $payment): Subscription
    {
        return DB::transaction(function () use ($clinic, $plan, $payment) {
            $subscription = $clinic->subscription ?? $this->createTrial($clinic);

            $subscription->update([
                'plan' => $plan->value,
                'status' => SubscriptionStatus::Active->value,
                'current_period_start' => $payment->period_start ?? now(),
                'current_period_end' => $payment->period_end ?? now()->addMonth(),
                'seats_limit' => $plan->seatsLimit(),
                'patients_limit' => $plan->patientsLimit(),
                'cancelled_at' => null,
            ]);

            $payment->update([
                'subscription_id' => $subscription->id,
                'status' => 'approved',
                'verified_at' => now(),
            ]);

            $this->syncClinicDenormalized($clinic, $subscription);

            return $subscription;
        });
    }

    public function extend(Clinic $clinic, SubscriptionPayment $payment): Subscription
    {
        return DB::transaction(function () use ($clinic, $payment) {
            $subscription = $clinic->subscription;

            $baseDate = $subscription->current_period_end && $subscription->current_period_end->isFuture()
                ? $subscription->current_period_end
                : now();

            $subscription->update([
                'status' => SubscriptionStatus::Active->value,
                'current_period_start' => $payment->period_start ?? $baseDate,
                'current_period_end' => $payment->period_end ?? $baseDate->addMonth(),
                'cancelled_at' => null,
            ]);

            $payment->update([
                'subscription_id' => $subscription->id,
                'status' => 'approved',
                'verified_at' => now(),
            ]);

            $this->syncClinicDenormalized($clinic, $subscription);

            return $subscription;
        });
    }

    public function markPastDue(Clinic $clinic): Subscription
    {
        $subscription = $clinic->subscription;
        $subscription->update(['status' => SubscriptionStatus::PastDue->value]);
        $this->syncClinicDenormalized($clinic, $subscription);

        return $subscription;
    }

    public function suspend(Clinic $clinic): Subscription
    {
        $subscription = $clinic->subscription;
        $subscription->update(['status' => SubscriptionStatus::Suspended->value]);
        $this->syncClinicDenormalized($clinic, $subscription);

        return $subscription;
    }

    public function cancel(Clinic $clinic): Subscription
    {
        $subscription = $clinic->subscription;
        $subscription->update([
            'status' => SubscriptionStatus::Cancelled->value,
            'cancelled_at' => now(),
        ]);
        $this->syncClinicDenormalized($clinic, $subscription);

        return $subscription;
    }

    public function changePlan(Clinic $clinic, Plan $plan): Subscription
    {
        $subscription = $clinic->subscription;
        $subscription->update([
            'plan' => $plan->value,
            'seats_limit' => $plan->seatsLimit(),
            'patients_limit' => $plan->patientsLimit(),
        ]);

        return $subscription;
    }

    protected function syncClinicDenormalized(Clinic $clinic, Subscription $subscription): void
    {
        DB::table('tenants')
            ->where('id', $clinic->id)
            ->update([
                'plan' => $subscription->plan->value,
                'subscription_status' => $subscription->status->value,
                'trial_ends_at' => $subscription->trial_ends_at,
            ]);
    }
}
