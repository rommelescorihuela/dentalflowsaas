<?php

namespace App\Filament\App\Resources\SystemActivities\Pages;

use App\Filament\App\Resources\SystemActivities\SystemActivityResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSystemActivity extends EditRecord
{
    protected static string $resource = SystemActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
