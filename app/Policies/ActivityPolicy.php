<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Activity;
use App\Traits\HasSpatiePermissions;
use Illuminate\Foundation\Auth\User as AuthUser;

class ActivityPolicy
{
    use HasSpatiePermissions;

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Activity');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Activity');
    }

    public function replicate(AuthUser $authUser, Activity $model): bool
    {
        return $authUser->can('Replicate:Activity');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Activity');
    }
}
