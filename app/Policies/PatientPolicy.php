<?php
 
namespace App\Policies;
 
use App\Models\Patient;
use App\Models\User;
 
class PatientPolicy
{
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
        return true;
    }

    public function update(User $user, Patient $patient): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // If no doctor is assigned, anyone can edit (to assign the patient)
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

        return $user->id === $patient->doctor_id;
    }
}