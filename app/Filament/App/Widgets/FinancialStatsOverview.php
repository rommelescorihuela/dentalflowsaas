<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Revenue This Month
        $revenueThisMonth = \App\Models\Payment::whereBetween('paid_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->sum('amount');

        $revenueLastMonth = \App\Models\Payment::whereBetween('paid_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ])->sum('amount');

        $revenueTrend = $revenueLastMonth > 0
            ? (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100
            : 0;

        // 2. Outstanding (Accounts Receivable)
        $totalAcceptedBudgets = \App\Models\Budget::where('status', 'accepted')->sum('total');
        $totalPayments = \App\Models\Payment::sum('amount');
        $outstanding = $totalAcceptedBudgets - $totalPayments;

        // 3. Acceptance Rate
        $totalSent = \App\Models\Budget::whereIn('status', ['sent', 'accepted'])->count();
        $totalAccepted = \App\Models\Budget::where('status', 'accepted')->count();
        $acceptanceRate = $totalSent > 0 ? ($totalAccepted / $totalSent) * 100 : 0;

        return [
            Stat::make('Ingresos (Mes)', '$' . number_format($revenueThisMonth, 2))
                ->description(number_format(abs($revenueTrend), 1) . '% ' . ($revenueTrend >= 0 ? 'subida' : 'bajada'))
                ->descriptionIcon($revenueTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart([$revenueLastMonth, $revenueThisMonth])
                ->color($revenueTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Por Cobrar', '$' . number_format($outstanding, 2))
                ->description('Total deuda de pacientes')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),

            Stat::make('Tasa de Aceptación', number_format($acceptanceRate, 1) . '%')
                ->description('Presupuestos aceptados')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('primary'),
        ];
    }
}
