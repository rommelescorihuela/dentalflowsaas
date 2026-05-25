<?php
 
namespace App\Policies;
 
use App\Models\User;
use App\Models\ClinicalRecord;
 
class ClinicalRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ClinicalRecord $clinicalRecord): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ClinicalRecord $clinicalRecord): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $patient = $clinicalRecord->patient;
        if (!$patient) {
            return false;
        }

        if (is_null($patient->doctor_id)) {
            return true;
        }

        return $user->id === $patient->doctor_id;
    }

    public function delete(User $user, ClinicalRecord $clinicalRecord): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $patient = $clinicalRecord->patient;
        if (!$patient) {
            return false;
        }

        if (is_null($patient->doctor_id)) {
            return true;
        }

        return $user->id === $patient->doctor_id;
    }
}
