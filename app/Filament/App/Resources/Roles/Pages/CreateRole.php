<?php

namespace App\Filament\App\Resources\Roles\Pages;

use App\Filament\App\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['clinic_id'])) {
            setPermissionsTeamId($data['clinic_id']);
        }

        return $data;
    }
}
