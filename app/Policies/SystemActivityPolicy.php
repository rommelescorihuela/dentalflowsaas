<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SystemActivity;
use App\Traits\HasSpatiePermissions;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SystemActivityPolicy
{
    use HandlesAuthorization;
    use HasSpatiePermissions;

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
