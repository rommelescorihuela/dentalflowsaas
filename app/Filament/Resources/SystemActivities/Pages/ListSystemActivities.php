<?php

namespace App\Filament\Resources\SystemActivities\Pages;

use App\Filament\Resources\SystemActivities\SystemActivityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSystemActivities extends ListRecords
{
    protected static string $resource = SystemActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
