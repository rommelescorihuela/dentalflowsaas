<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;

class BudgetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Budget');
    }

    public function view(User $user, Budget $budget): bool
    {
        return $user->can('View:Budget');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Budget');
    }

    public function update(User $user, Budget $budget): bool
    {
        return $user->can('Update:Budget');
    }

    public function delete(User $user, Budget $budget): bool
    {
        return $user->can('Delete:Budget');
    }

    public function restore(User $user, Budget $budget): bool
    {
        return $user->can('Restore:Budget');
    }

    public function forceDelete(User $user, Budget $budget): bool
    {
        return $user->can('ForceDelete:Budget');
    }
}
