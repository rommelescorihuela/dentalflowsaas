<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

class Domain extends BaseDomain
{
    public static function tenantIdColumn(): string
    {
        return 'clinic_id';
    }

    public function tenant()
    {
        return $this->belongsTo(config('tenancy.tenant_model'), 'clinic_id');
    }
}
