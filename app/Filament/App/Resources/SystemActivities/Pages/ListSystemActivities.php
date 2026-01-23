<?php

namespace App\Filament\App\Resources\SystemActivities\Pages;

use App\Filament\App\Resources\SystemActivities\SystemActivityResource;
use Filament\Resources\Pages\ListRecords;

class ListSystemActivities extends ListRecords
{
    protected static string $resource = SystemActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action
        ];
    }
}
