<?php

namespace App\Policies;

use App\Models\ProcedurePrice;
use App\Models\User;

class ProcedurePricePolicy
{
    use \App\Traits\HasSpatiePermissions;
}
