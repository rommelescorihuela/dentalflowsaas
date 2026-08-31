<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ServiceFeedback;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceFeedbackPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ServiceFeedback');
    }

    public function view(AuthUser $authUser, ServiceFeedback $serviceFeedback): bool
    {
        return $authUser->can('View:ServiceFeedback');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ServiceFeedback');
    }

    public function update(AuthUser $authUser, ServiceFeedback $serviceFeedback): bool
    {
        return $authUser->can('Update:ServiceFeedback');
    }

    public function delete(AuthUser $authUser, ServiceFeedback $serviceFeedback): bool
    {
        return $authUser->can('Delete:ServiceFeedback');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ServiceFeedback');
    }

    public function restore(AuthUser $authUser, ServiceFeedback $serviceFeedback): bool
    {
        return $authUser->can('Restore:ServiceFeedback');
    }

    public function forceDelete(AuthUser $authUser, ServiceFeedback $serviceFeedback): bool
    {
        return $authUser->can('ForceDelete:ServiceFeedback');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ServiceFeedback');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ServiceFeedback');
    }

    public function replicate(AuthUser $authUser, ServiceFeedback $serviceFeedback): bool
    {
        return $authUser->can('Replicate:ServiceFeedback');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ServiceFeedback');
    }

}