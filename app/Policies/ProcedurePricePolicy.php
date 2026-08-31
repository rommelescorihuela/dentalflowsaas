<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProcedurePrice;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProcedurePricePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProcedurePrice');
    }

    public function view(AuthUser $authUser, ProcedurePrice $procedurePrice): bool
    {
        return $authUser->can('View:ProcedurePrice');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProcedurePrice');
    }

    public function update(AuthUser $authUser, ProcedurePrice $procedurePrice): bool
    {
        return $authUser->can('Update:ProcedurePrice');
    }

    public function delete(AuthUser $authUser, ProcedurePrice $procedurePrice): bool
    {
        return $authUser->can('Delete:ProcedurePrice');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProcedurePrice');
    }

    public function restore(AuthUser $authUser, ProcedurePrice $procedurePrice): bool
    {
        return $authUser->can('Restore:ProcedurePrice');
    }

    public function forceDelete(AuthUser $authUser, ProcedurePrice $procedurePrice): bool
    {
        return $authUser->can('ForceDelete:ProcedurePrice');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProcedurePrice');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProcedurePrice');
    }

    public function replicate(AuthUser $authUser, ProcedurePrice $procedurePrice): bool
    {
        return $authUser->can('Replicate:ProcedurePrice');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProcedurePrice');
    }

}