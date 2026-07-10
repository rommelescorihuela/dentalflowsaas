<?php

namespace App\Filament\App\Resources\ClinicalHistories\Pages;

use App\Filament\App\Resources\ClinicalHistories\ClinicalHistoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClinicalHistory extends CreateRecord
{
    protected static string $resource = ClinicalHistoryResource::class;
}
