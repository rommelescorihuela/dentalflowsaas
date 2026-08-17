<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use App\Services\PlanLimits;

class PatientPolicy
{
    public function __construct(
        protected PlanLimits $planLimits
    ) {}

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Patient $patient): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $clinic = $user->tenant;

        if (! $clinic) {
            return true;
        }

        return $this->planLimits->canCreatePatient($clinic);
    }

    public function update(User $user, Patient $patient): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if (is_null($patient->doctor_id)) {
            return true;
        }

        return $user->id === $patient->doctor_id;
    }

    public function delete(User $user, Patient $patient): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if (is_null($patient->doctor_id)) {
            return true;
        }

        return $user->id === $patient->doctor_id;
    }
}
