<?php

namespace App\Filament\App\Resources\Roles\Pages;

use App\Filament\App\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['clinic_id'])) {
            setPermissionsTeamId($data['clinic_id']);
        }

        return $data;
    }
}
