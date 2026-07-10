<?php

namespace App\Filament\App\Resources\ClinicalHistories\Pages;

use App\Filament\App\Resources\ClinicalHistories\ClinicalHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClinicalHistory extends EditRecord
{
    protected static string $resource = ClinicalHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
