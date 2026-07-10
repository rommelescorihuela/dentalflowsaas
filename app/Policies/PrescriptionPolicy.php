<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\User;

class PrescriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Prescription $prescription): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        if ($user->hasRole('doctor')) {
            $patient = $prescription->patient;
            if (! $patient) {
                return false;
            }

            if (is_null($patient->doctor_id)) {
                return true;
            }

            return $user->id === $patient->doctor_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Prescription $prescription): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $patient = $prescription->patient;
        if (! $patient) {
            return false;
        }

        if (is_null($patient->doctor_id)) {
            return true;
        }

        return $user->id === $patient->doctor_id;
    }

    public function delete(User $user, Prescription $prescription): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $patient = $prescription->patient;
        if (! $patient) {
            return false;
        }

        if (is_null($patient->doctor_id)) {
            return true;
        }

        return $user->id === $patient->doctor_id;
    }
}
