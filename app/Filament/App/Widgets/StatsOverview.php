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
        return [
            Stat::make('Patients', Patient::count())
                ->description('Total patients registered')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Appointments', Appointment::where('start_time', '>=', now())->count())
                ->description('Upcoming appointments')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
            Stat::make('Pending Budgets', Budget::where('status', 'sent')->count())
                ->description('Budgets waiting for approval')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),
        ];
    }
}
