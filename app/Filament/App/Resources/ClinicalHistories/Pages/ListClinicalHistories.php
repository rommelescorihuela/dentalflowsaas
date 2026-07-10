<?php

namespace App\Filament\App\Resources\ClinicalHistories\Pages;

use App\Filament\App\Resources\ClinicalHistories\ClinicalHistoryResource;
use Filament\Resources\Pages\ListRecords;

class ListClinicalHistories extends ListRecords
{
    protected static string $resource = ClinicalHistoryResource::class;
}
