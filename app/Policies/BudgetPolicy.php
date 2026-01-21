<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;

class BudgetPolicy
{
    use \App\Traits\HasSpatiePermissions;
}
