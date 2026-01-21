<?php

namespace App\Filament\App\Resources\ProcedurePrices\Pages;

use App\Filament\App\Resources\ProcedurePrices\ProcedurePriceResource;
use Filament\Resources\Pages\ListRecords;

class ListProcedurePrices extends ListRecords
{
    protected static string $resource = ProcedurePriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
