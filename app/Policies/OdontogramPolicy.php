<?php
 
namespace App\Policies;
 
use App\Models\User;
use App\Models\Odontogram;
 
class OdontogramPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Odontogram $odontogram): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Odontogram $odontogram): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $patient = $odontogram->patient;
        if (!$patient) {
            return false;
        }

        if (is_null($patient->doctor_id)) {
            return true;
        }

        return $user->id === $patient->doctor_id;
    }

    public function delete(User $user, Odontogram $odontogram): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $patient = $odontogram->patient;
        if (!$patient) {
            return false;
        }

        if (is_null($patient->doctor_id)) {
            return true;
        }

        return $user->id === $patient->doctor_id;
    }
}
