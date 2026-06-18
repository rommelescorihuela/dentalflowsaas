<?php

namespace App\Policies;

use App\Models\User;
use App\Services\PlanLimits;

class UserPolicy
{
    use \App\Traits\HasSpatiePermissions;

    public function create(User $user): bool
    {
        if (! $user->can('Create:User')) {
            return false;
        }

        if ($user->hasRole('super-admin')) {
            return true;
        }

        $clinic = $user->tenant;

        if (! $clinic) {
            return true;
        }

        return app(PlanLimits::class)->canInviteUser($clinic);
    }
}
