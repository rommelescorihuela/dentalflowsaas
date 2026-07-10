<?php

namespace App\Filament\App\Resources\Prescriptions\Pages;

use App\Filament\App\Resources\Prescriptions\PrescriptionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrescription extends CreateRecord
{
    protected static string $resource = PrescriptionResource::class;
}
