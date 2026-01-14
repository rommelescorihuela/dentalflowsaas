<?php

namespace App\Filament\App\Resources\Patients\Pages;

use App\Filament\App\Resources\Patients\PatientResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;
}
