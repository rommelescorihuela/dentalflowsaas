<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Budget;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected ?string $heading = 'Resumen General';

    protected function getStats(): array
    {
        return [
            Stat::make('Pacientes', Patient::count())
                ->description('Total de pacientes registrados')
                ->descriptionIcon('heroicon-m-users')
                ->icon('heroicon-o-users')
                ->color('success'),
            Stat::make('Citas', Appointment::where('start_time', '>=', now())->count())
                ->description('Próximas citas')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->icon('heroicon-o-calendar-days')
                ->color('primary'),
            Stat::make('Presupuestos Pendientes', Budget::where('status', 'sent')->count())
                ->description('Presupuestos esperando aprobación')
                ->descriptionIcon('heroicon-m-document-currency-dollar')
                ->icon('heroicon-o-document-currency-dollar')
                ->color('warning'),
        ];
    }
}
