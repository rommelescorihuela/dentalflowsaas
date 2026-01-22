<?php

namespace App\Filament\Resources\SystemActivities\Pages;

use App\Filament\Resources\SystemActivities\SystemActivityResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSystemActivity extends ViewRecord
{
    protected static string $resource = SystemActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
