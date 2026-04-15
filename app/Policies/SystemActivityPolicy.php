<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SystemActivity;
use Illuminate\Auth\Access\HandlesAuthorization;

class SystemActivityPolicy
{
    use HandlesAuthorization;
    use \App\Traits\HasSpatiePermissions;

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SystemActivity');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SystemActivity');
    }

    public function replicate(AuthUser $authUser, SystemActivity $model): bool
    {
        return $authUser->can('Replicate:SystemActivity');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SystemActivity');
    }
}
