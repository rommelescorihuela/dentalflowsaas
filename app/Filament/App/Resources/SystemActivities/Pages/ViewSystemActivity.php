<?php

namespace App\Filament\App\Resources\SystemActivities\Pages;

use App\Filament\App\Resources\SystemActivities\SystemActivityResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSystemActivity extends ViewRecord
{
    protected static string $resource = SystemActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No edit action
        ];
    }
}
