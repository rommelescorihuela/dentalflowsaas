<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\Clinic;
use App\Notifications\GracePeriodEndingNotification;
use App\Notifications\SubscriptionSuspendedNotification;
use App\Notifications\TrialExpiredNotification;
use App\Notifications\TrialExpiringNotification;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class ProcessSubscriptions extends Command
{
    protected $signature = 'subscriptions:process';

    protected $description = 'Procesa suscripciones: expira trials, suspende morosos, envía notificaciones';

    public function handle(SubscriptionService $service): int
    {
        $now = now();
        $graceDays = 7;

        $subscriptions = \App\Models\Subscription::with('clinic')->get();

        $trialExpired = 0;
        $suspended = 0;
        $pastDue = 0;
        $notified = 0;

        foreach ($subscriptions as $subscription) {
            $clinic = $subscription->clinic;

            // 1. Trialing: notify 3 days before trial ends
            if ($subscription->status === SubscriptionStatus::Trialing) {
                if ($subscription->trial_ends_at && $subscription->trial_ends_at <= $now) {
                    $service->markPastDue($clinic);
                    $trialExpired++;
                    $this->notifyClinicAdmins($clinic, new TrialExpiredNotification);
                    $notified++;
                } elseif ($subscription->trial_ends_at && $subscription->trial_ends_at->subDays(3) <= $now) {
                    $daysRemaining = max(1, (int) $now->diffInDays($subscription->trial_ends_at));
                    $this->notifyClinicAdmins($clinic, new TrialExpiringNotification($daysRemaining));
                    $notified++;
                }
            }

            // 2. Active: check if period ended
            if ($subscription->status === SubscriptionStatus::Active) {
                if ($subscription->current_period_end && $subscription->current_period_end < $now->toDateString()) {
                    $service->markPastDue($clinic);
                    $pastDue++;
                }
            }

            // 3. PastDue: check grace period, notify 3 days before suspension
            if ($subscription->status === SubscriptionStatus::PastDue) {
                $baseDate = $subscription->trial_ends_at ?? $subscription->current_period_end;
                $suspendDate = $baseDate?->addDays($graceDays);

                if ($suspendDate && $suspendDate <= $now) {
                    $service->suspend($clinic);
                    $suspended++;
                    $this->notifyClinicAdmins($clinic, new SubscriptionSuspendedNotification);
                    $notified++;
                } elseif ($suspendDate && $suspendDate->subDays(3) <= $now) {
                    $daysRemaining = max(1, (int) $now->diffInDays($suspendDate));
                    $this->notifyClinicAdmins($clinic, new GracePeriodEndingNotification($daysRemaining));
                    $notified++;
                }
            }
        }

        $this->info("Procesadas: {$subscriptions->count()} suscripciones");
        $this->info("Trials expirados → past_due: {$trialExpired}");
        $this->info("Activos → past_due: {$pastDue}");
        $this->info("Suspendidos: {$suspended}");
        $this->info("Notificaciones enviadas: {$notified}");

        return Command::SUCCESS;
    }

    protected function notifyClinicAdmins(Clinic $clinic, $notification): void
    {
        $admins = $clinic->users()
            ->whereHas('roles', fn ($q) => $q->where('name', 'admin'))
            ->orWhereHas('roles', fn ($q) => $q->where('name', 'super-admin'))
            ->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, $notification);
        }
    }
}
