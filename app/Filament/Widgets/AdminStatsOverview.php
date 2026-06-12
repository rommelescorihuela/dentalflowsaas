<?php

namespace App\Filament\Widgets;

use App\Models\Clinic;
use App\Models\User;
use App\Models\SubscriptionPayment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected ?string $heading = 'Resumen del Sistema';

    protected function getStats(): array
    {
        $totalClinics = Clinic::count();
        $totalUsers = User::count();
        $activeUsers = User::whereNull('clinic_id')->orWhereHas('clinic')->count();

        $thisMonth = SubscriptionPayment::whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $lastMonth = SubscriptionPayment::whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('amount');

        $trend = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : 0;

        return [
            Stat::make('Clinicas', $totalClinics)
                ->description('Total de clinicas registradas')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->icon('heroicon-o-building-office-2')
                ->color('primary')
                ->chart(Clinic::selectRaw('COUNT(*) as count, DATE_TRUNC(\'month\', created_at) as month')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('count')
                    ->toArray()),

            Stat::make('Usuarios', $totalUsers)
                ->description("{$activeUsers} usuarios activos")
                ->descriptionIcon('heroicon-m-users')
                ->icon('heroicon-o-users')
                ->color('success')
                ->chart(User::selectRaw('COUNT(*) as count, DATE_TRUNC(\'month\', created_at) as month')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('count')
                    ->toArray()),

            Stat::make('Ingresos del Mes', '$' . number_format($thisMonth, 0, ',', '.'))
                ->description($trend >= 0 ? "+{$trend}% vs mes anterior" : "{$trend}% vs mes anterior")
                ->descriptionIcon($trend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->icon('heroicon-o-banknotes')
                ->color($trend >= 0 ? 'success' : 'danger'),
        ];
    }
}
