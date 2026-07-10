<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Clinic;
use App\Traits\HasSpatiePermissions;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ClinicPolicy
{
    use HandlesAuthorization;
    use HasSpatiePermissions;

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Clinic');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Clinic');
    }

    public function replicate(AuthUser $authUser, Clinic $clinic): bool
    {
        return $authUser->can('Replicate:Clinic');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Clinic');
    }
}
