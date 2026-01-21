<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;

class InventoryPolicy
{
    use \App\Traits\HasSpatiePermissions;
}
