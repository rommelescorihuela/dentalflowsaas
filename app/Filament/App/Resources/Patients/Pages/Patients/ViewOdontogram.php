<?php

namespace App\Filament\App\Resources\Patients\Pages\Patients;

use App\Filament\App\Resources\Patients\PatientResource;
use Filament\Resources\Pages\Page;

class ViewOdontogram extends Page
{
    protected static string $resource = PatientResource::class;

    protected string $view = 'filament.app.resources.patients.pages.patients.view-odontogram';
}
