<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Plan;
use App\Models\Clinic;
use App\Models\Subscription;

class PlanLimits
{
    public function getSubscription(Clinic $clinic): ?Subscription
    {
        return $clinic->subscription;
    }

    public function effectivePlan(Clinic $clinic): Plan
    {
        $subscription = $this->getSubscription($clinic);

        if (! $subscription) {
            return Plan::FreeTrial;
        }

        return $subscription->effectivePlan();
    }

    public function hasAccess(Clinic $clinic): bool
    {
        $subscription = $this->getSubscription($clinic);

        if (! $subscription) {
            return false;
        }

        return $subscription->hasAccess();
    }

    public function canCreatePatient(Clinic $clinic): bool
    {
        $plan = $this->effectivePlan($clinic);
        $limit = $plan->patientsLimit();

        if ($limit === null) {
            return true;
        }

        return $clinic->patients()->count() < $limit;
    }

    public function canInviteUser(Clinic $clinic): bool
    {
        $plan = $this->effectivePlan($clinic);
        $limit = $plan->seatsLimit();

        if ($limit === null) {
            return true;
        }

        return $clinic->users()->count() < $limit;
    }

    public function hasFeature(Clinic $clinic, string $feature): bool
    {
        return $this->effectivePlan($clinic)->hasFeature($feature);
    }
}
