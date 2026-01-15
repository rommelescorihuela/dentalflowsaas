<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Appointment');
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $user->can('View:Appointment');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Appointment');
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->can('Update:Appointment');
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->can('Delete:Appointment');
    }

    public function restore(User $user, Appointment $appointment): bool
    {
        return $user->can('Restore:Appointment');
    }

    public function forceDelete(User $user, Appointment $appointment): bool
    {
        return $user->can('ForceDelete:Appointment');
    }
}
