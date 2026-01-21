<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSpatiePermissions
{
    /**
     * Get the model name for permission checks.
     * by default it uses the class basename (e.g. 'Inventory' from 'App\Models\Inventory')
     */
    protected function getModelName(): string
    {
        // Try to guess the model from the policy name if possible, or assume the mapping is consistent
        // Or simply allow overriding.
        // For methods receiving a Model instance, we can use that.
        // For viewAny/create, we rely on convention.

        $policyClass = class_basename($this);
        return Str::replaceLast('Policy', '', $policyClass);
    }

    public function viewAny(User $user): bool
    {
        return $user->can("ViewAny:{$this->getModelName()}");
    }

    public function view(User $user, Model $model): bool
    {
        return $user->can("View:{$this->getModelName()}");
    }

    public function create(User $user): bool
    {
        return $user->can("Create:{$this->getModelName()}");
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can("Update:{$this->getModelName()}");
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can("Delete:{$this->getModelName()}");
    }

    public function restore(User $user, Model $model): bool
    {
        return $user->can("Restore:{$this->getModelName()}");
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $user->can("ForceDelete:{$this->getModelName()}");
    }
}
