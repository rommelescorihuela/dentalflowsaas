<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Patient');
    }

    public function view(User $user, Patient $patient): bool
    {
        return $user->can('View:Patient');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Patient');
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->can('Update:Patient');
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->can('Delete:Patient');
    }

    public function restore(User $user, Patient $patient): bool
    {
        return $user->can('Restore:Patient');
    }

    public function forceDelete(User $user, Patient $patient): bool
    {
        return $user->can('ForceDelete:Patient');
    }
}
