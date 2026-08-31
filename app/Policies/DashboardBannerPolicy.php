<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DashboardBanner;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardBannerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DashboardBanner');
    }

    public function view(AuthUser $authUser, DashboardBanner $dashboardBanner): bool
    {
        return $authUser->can('View:DashboardBanner');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DashboardBanner');
    }

    public function update(AuthUser $authUser, DashboardBanner $dashboardBanner): bool
    {
        return $authUser->can('Update:DashboardBanner');
    }

    public function delete(AuthUser $authUser, DashboardBanner $dashboardBanner): bool
    {
        return $authUser->can('Delete:DashboardBanner');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:DashboardBanner');
    }

    public function restore(AuthUser $authUser, DashboardBanner $dashboardBanner): bool
    {
        return $authUser->can('Restore:DashboardBanner');
    }

    public function forceDelete(AuthUser $authUser, DashboardBanner $dashboardBanner): bool
    {
        return $authUser->can('ForceDelete:DashboardBanner');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DashboardBanner');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DashboardBanner');
    }

    public function replicate(AuthUser $authUser, DashboardBanner $dashboardBanner): bool
    {
        return $authUser->can('Replicate:DashboardBanner');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DashboardBanner');
    }

}