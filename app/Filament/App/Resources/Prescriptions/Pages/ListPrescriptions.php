<?php

namespace App\Filament\App\Resources\Prescriptions\Pages;

use App\Filament\App\Resources\Prescriptions\PrescriptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrescriptions extends ListRecords
{
    protected static string $resource = PrescriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
