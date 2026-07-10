<?php

namespace App\Policies;

use App\Models\PatientMedicalHistory;
use App\Models\User;

class PatientMedicalHistoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PatientMedicalHistory $record): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        if ($user->hasRole('doctor')) {
            $patient = $record->patient;
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

    public function update(User $user, PatientMedicalHistory $record): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $patient = $record->patient;
        if (! $patient) {
            return false;
        }

        if (is_null($patient->doctor_id)) {
            return true;
        }

        return $user->id === $patient->doctor_id;
    }

    public function delete(User $user, PatientMedicalHistory $record): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $patient = $record->patient;
        if (! $patient) {
            return false;
        }

        if (is_null($patient->doctor_id)) {
            return true;
        }

        return $user->id === $patient->doctor_id;
    }
}
