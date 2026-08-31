<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PatientMedicalHistory;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatientMedicalHistoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PatientMedicalHistory');
    }

    public function view(AuthUser $authUser, PatientMedicalHistory $patientMedicalHistory): bool
    {
        return $authUser->can('View:PatientMedicalHistory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PatientMedicalHistory');
    }

    public function update(AuthUser $authUser, PatientMedicalHistory $patientMedicalHistory): bool
    {
        return $authUser->can('Update:PatientMedicalHistory');
    }

    public function delete(AuthUser $authUser, PatientMedicalHistory $patientMedicalHistory): bool
    {
        return $authUser->can('Delete:PatientMedicalHistory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PatientMedicalHistory');
    }

    public function restore(AuthUser $authUser, PatientMedicalHistory $patientMedicalHistory): bool
    {
        return $authUser->can('Restore:PatientMedicalHistory');
    }

    public function forceDelete(AuthUser $authUser, PatientMedicalHistory $patientMedicalHistory): bool
    {
        return $authUser->can('ForceDelete:PatientMedicalHistory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PatientMedicalHistory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PatientMedicalHistory');
    }

    public function replicate(AuthUser $authUser, PatientMedicalHistory $patientMedicalHistory): bool
    {
        return $authUser->can('Replicate:PatientMedicalHistory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PatientMedicalHistory');
    }

}