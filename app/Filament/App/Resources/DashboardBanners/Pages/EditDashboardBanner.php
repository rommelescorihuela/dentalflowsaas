<?php

namespace App\Filament\App\Resources\DashboardBanners\Pages;

use App\Filament\App\Resources\DashboardBanners\DashboardBannerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDashboardBanner extends EditRecord
{
    protected static string $resource = DashboardBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
