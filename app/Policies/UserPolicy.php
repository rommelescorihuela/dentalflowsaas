<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    use \App\Traits\HasSpatiePermissions;
}
