<?php

namespace App\Filament\App\Resources\Inventory\Pages;

use App\Filament\App\Resources\Inventory\InventoryResource;
use Filament\Resources\Pages\ListRecords;

class ListInventories extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
