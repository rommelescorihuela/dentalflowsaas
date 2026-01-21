<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Clinic extends BaseTenant
{
    use HasDomains;

    public function domains()
    {
        return $this->hasMany(config('tenancy.domain_model'), 'clinic_id');
    }

    /**
     * Definimos las columnas personalizadas que tiene nuestra tabla 'clinics'.
     * Esto es necesario para que el paquete sepa qué campos guardar en la tabla.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'plan',
            'data',
        ];
    }
}
