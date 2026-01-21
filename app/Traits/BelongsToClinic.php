<?php

namespace App\Traits;

use App\Scopes\ClinicScope;

trait BelongsToClinic
{
    public static $tenantIdColumn = 'clinic_id';

    public function tenant()
    {
        return $this->belongsTo(config('tenancy.tenant_model'), static::$tenantIdColumn);
    }

    public static function bootBelongsToClinic()
    {
        static::addGlobalScope(new ClinicScope);

        static::creating(function ($model) {
            if (!$model->getAttribute(static::$tenantIdColumn) && !$model->relationLoaded('tenant')) {
                if (tenancy()->initialized) {
                    $model->setAttribute(static::$tenantIdColumn, tenant()->getTenantKey());
                    $model->setRelation('tenant', tenant());
                }
            }
        });
    }

    public static function getTenantKeyName(): string
    {
        return 'clinic_id';
    }

    public function clinic()
    {
        return $this->tenant();
    }
}
