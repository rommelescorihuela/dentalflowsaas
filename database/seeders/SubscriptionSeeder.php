<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Plan;
use App\Enums\SubscriptionStatus;
use App\Models\Clinic;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $clinic1 = Clinic::find('clinic1');
        $clinic2 = Clinic::find('clinic2');
        $clinic3 = Clinic::find('clinic3');
        $clinic4 = Clinic::find('clinic4');

        if ($clinic1) {
            $this->seedActiveSubscription($clinic1, Plan::Pro);
        }

        if ($clinic2) {
            $this->seedActiveSubscription($clinic2, Plan::Basic);
        }

        if ($clinic3) {
            $this->seedTrialSubscription($clinic3);
        }

        if ($clinic4) {
            $this->seedSuspendedSubscription($clinic4, Plan::Basic);
        }
    }

    protected function seedActiveSubscription(Clinic $clinic, Plan $plan): void
    {
        $subscription = Subscription::firstOrCreate(
            ['clinic_id' => $clinic->id],
            [
                'plan' => $plan->value,
                'status' => SubscriptionStatus::Active->value,
                'current_period_start' => now()->startOfMonth(),
                'current_period_end' => now()->addMonth(),
                'seats_limit' => $plan->seatsLimit(),
                'patients_limit' => $plan->patientsLimit(),
            ]
        );

        $clinic->update([
            'plan' => $plan->value,
            'subscription_status' => SubscriptionStatus::Active->value,
        ]);

        // 3 pagos históricos aprobados
        for ($i = 3; $i >= 1; $i--) {
            SubscriptionPayment::create([
                'clinic_id' => $clinic->id,
                'subscription_id' => $subscription->id,
                'amount' => $plan->priceUsd() ?? 99,
                'currency' => 'USD',
                'method' => ['zelle_usd', 'bank_transfer_bs', 'pago_movil_bs'][rand(0, 2)],
                'status' => 'approved',
                'reference' => 'SEED-'.$clinic->id.'-'.$i,
                'period_start' => now()->subMonths($i)->startOfMonth(),
                'period_end' => now()->subMonths($i - 1)->startOfMonth(),
                'paid_at' => now()->subMonths($i),
                'verified_at' => now()->subMonths($i),
            ]);
        }

        // 1 pago pendiente para demo del workflow
        SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'subscription_id' => $subscription->id,
            'amount' => $plan->priceUsd() ?? 99,
            'currency' => 'USD',
            'method' => 'binance_usdt',
            'status' => 'pending',
            'reference' => 'USDT-DEMO-'.$clinic->id,
            'paid_at' => now()->subDay(),
        ]);
    }

    protected function seedTrialSubscription(Clinic $clinic): void
    {
        Subscription::firstOrCreate(
            ['clinic_id' => $clinic->id],
            [
                'plan' => Plan::FreeTrial->value,
                'status' => SubscriptionStatus::Trialing->value,
                'trial_ends_at' => now()->addDays(10),
            ]
        );

        $clinic->update([
            'plan' => Plan::FreeTrial->value,
            'subscription_status' => SubscriptionStatus::Trialing->value,
            'trial_ends_at' => now()->addDays(10),
        ]);
    }

    protected function seedSuspendedSubscription(Clinic $clinic, Plan $plan): void
    {
        $subscription = Subscription::firstOrCreate(
            ['clinic_id' => $clinic->id],
            [
                'plan' => $plan->value,
                'status' => SubscriptionStatus::Suspended->value,
                'trial_ends_at' => now()->subDays(30),
                'current_period_end' => now()->subDays(20),
            ]
        );

        $clinic->update([
            'plan' => $plan->value,
            'subscription_status' => SubscriptionStatus::Suspended->value,
            'trial_ends_at' => now()->subDays(30),
        ]);

        // 1 pago rechazado para demo
        SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'subscription_id' => $subscription->id,
            'amount' => $plan->priceUsd() ?? 49,
            'currency' => 'Bs',
            'method' => 'bank_transfer_bs',
            'status' => 'rejected',
            'reference' => 'REJECTED-'.$clinic->id,
            'paid_at' => now()->subDays(25),
        ]);
    }
}
