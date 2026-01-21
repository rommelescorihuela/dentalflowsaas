<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToClinic;

class Treatment extends Model
{
    use BelongsToClinic;
}
