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

    protected function getStats(): array
    {
        $todayPatients = Patient::whereDate('created_at', today())->count();
        $todayAppointments = Appointment::whereDate('start_time', today())->count();
        $pendingBudgets = Budget::where('status', 'sent')->count();

        return [
            Stat::make('Pacientes', Patient::count())
                ->description($todayPatients > 0 ? $todayPatients . ' nuevos hoy' : 'Sin nuevos hoy')
                ->descriptionIcon($todayPatients > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-user-plus')
                ->color('success'),
            Stat::make('Citas', Appointment::where('start_time', '>=', now())->count())
                ->description($todayAppointments > 0 ? $todayAppointments . ' para hoy' : 'Sin citas hoy')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
            Stat::make('Presupuestos Pendientes', $pendingBudgets)
                ->description($pendingBudgets > 0 ? 'Esperando aprobación' : 'Sin pendientes')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),
        ];
    }
}
