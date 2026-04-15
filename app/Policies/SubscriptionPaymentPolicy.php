<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SubscriptionPayment;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPaymentPolicy
{
    use HandlesAuthorization;
    use \App\Traits\HasSpatiePermissions;

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SubscriptionPayment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SubscriptionPayment');
    }

    public function replicate(AuthUser $authUser, SubscriptionPayment $model): bool
    {
        return $authUser->can('Replicate:SubscriptionPayment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SubscriptionPayment');
    }
}
