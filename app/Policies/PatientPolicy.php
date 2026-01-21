<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    use \App\Traits\HasSpatiePermissions;
}
