<?php

namespace App\Filament\App\Resources\Patients\Pages;

use App\Filament\App\Resources\Patients\PatientResource;
use App\Filament\App\Resources\Patients\Widgets\PatientAppointmentsChart;
use App\Filament\App\Resources\Patients\Widgets\PatientStatsOverview;
use App\Models\Patient;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class HealthProgress extends Page
{
    use InteractsWithRecord;

    protected static string $resource = PatientResource::class;

    protected string $view = 'filament.app.resources.patients.pages.health-progress';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PatientStatsOverview::class,
            PatientAppointmentsChart::class,
        ];
    }
}
