<?php
 
namespace App\Policies;
 
use App\Models\Appointment;
use App\Models\User;
 
class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $patient = $appointment->patient;
        if (!$patient) {
            return false;
        }

        if (is_null($patient->doctor_id)) {
            return true;
        }

        return $user->id === $patient->doctor_id;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $patient = $appointment->patient;
        if (!$patient) {
            return false;
        }

        if (is_null($patient->doctor_id)) {
            return true;
        }

        return $user->id === $patient->doctor_id;
    }
}
