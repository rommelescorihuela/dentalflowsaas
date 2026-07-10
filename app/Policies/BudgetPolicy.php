<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;

class BudgetPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Budget $budget): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Budget $budget): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $patient = $budget->patient;
        if (! $patient) {
            return false;
        }

        if (is_null($patient->doctor_id)) {
            return true;
        }

        return $user->id === $patient->doctor_id;
    }

    public function delete(User $user, Budget $budget): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $patient = $budget->patient;
        if (! $patient) {
            return false;
        }

        if (is_null($patient->doctor_id)) {
            return true;
        }

        return $user->id === $patient->doctor_id;
    }
}
