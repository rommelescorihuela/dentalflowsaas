<?php

namespace App\Filament\App\Widgets;

use App\Helpers\ClinicHelper;
use App\Models\Budget;
use App\Models\Clinic;
use App\Models\Payment;
use App\Services\PlanLimits;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Resumen Financiero';

    public static function canView(): bool
    {
        $tenant = tenant() ?? (Clinic::find(auth()->user()?->clinic_id));

        if (! $tenant) {
            return false;
        }

        return app(PlanLimits::class)->hasFeature($tenant, 'bi_reports');
    }

    protected function getStats(): array
    {
        // 1. Revenue This Month
        $revenueThisMonth = Payment::whereBetween('paid_at', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ])->sum('amount');

        $revenueLastMonth = Payment::whereBetween('paid_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth(),
        ])->sum('amount');

        $revenueTrend = $revenueLastMonth > 0
            ? (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100
            : 0;

        // 2. Outstanding (Accounts Receivable)
        $totalAcceptedBudgets = Budget::where('status', 'accepted')->sum('total');
        $totalPayments = Payment::sum('amount');
        $outstanding = $totalAcceptedBudgets - $totalPayments;

        // 3. Acceptance Rate
        $totalSent = Budget::whereIn('status', ['sent', 'accepted'])->count();
        $totalAccepted = Budget::where('status', 'accepted')->count();
        $acceptanceRate = $totalSent > 0 ? ($totalAccepted / $totalSent) * 100 : 0;

        return [
            Stat::make('Ingresos (Mes)', ClinicHelper::formatMoney($revenueThisMonth))
                ->description(number_format(abs($revenueTrend), 1).'% '.($revenueTrend >= 0 ? 'subida' : 'bajada'))
                ->descriptionIcon($revenueTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->icon($revenueTrend >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->chart([$revenueLastMonth, $revenueThisMonth])
                ->color($revenueTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Por Cobrar', ClinicHelper::formatMoney($outstanding))
                ->description('Total deuda de pacientes')
                ->descriptionIcon('heroicon-m-banknotes')
                ->icon('heroicon-o-banknotes')
                ->color('warning'),

            Stat::make('Tasa de Aceptación', number_format($acceptanceRate, 1).'%')
                ->description('Presupuestos aceptados')
                ->descriptionIcon('heroicon-m-check-badge')
                ->icon('heroicon-o-check-badge')
                ->color('primary'),
        ];
    }
}
