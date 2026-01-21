<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    use \App\Traits\HasSpatiePermissions;
}
