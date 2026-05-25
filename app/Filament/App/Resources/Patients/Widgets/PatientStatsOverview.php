<?php

namespace App\Filament\App\Resources\Patients\Widgets;

use App\Models\Patient;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Appointment;

class PatientStatsOverview extends BaseWidget
{
    public ?Patient $record = null;

    protected function getStats(): array
    {
        if (!$this->record) {
            return [];
        }

        $appointments = $this->record->appointments();

        return [
            Stat::make('Total de Citas', $appointments->count())
                ->icon('heroicon-o-calendar'),

            Stat::make('Tratamientos Completados', $appointments->where('status', 'completed')->count())
                ->description('Completados totalmente')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Pendientes/Programadas', $appointments->whereIn('status', ['scheduled', 'confirmed'])->count())
                ->description('Próximas')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
