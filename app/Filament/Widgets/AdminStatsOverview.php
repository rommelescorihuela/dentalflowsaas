<?php

namespace App\Filament\Widgets;

use App\Models\Clinic;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // MRR: suma de pagos aprobados del mes actual
        $mrr = SubscriptionPayment::where('status', 'approved')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        // MRR del mes anterior para calcular tendencia
        $lastMonth = now()->subMonth();
        $mrrLastMonth = SubscriptionPayment::where('status', 'approved')
            ->whereMonth('paid_at', $lastMonth->month)
            ->whereYear('paid_at', $lastMonth->year)
            ->sum('amount');

        $mrrTrend = $mrrLastMonth > 0
            ? round((($mrr - $mrrLastMonth) / $mrrLastMonth) * 100, 1)
            : 0;

        // Clínicas activas vs totales
        $totalClinics = Clinic::count();
        $activeClinics = Subscription::where('status', 'active')->count();

        // Tasa de conversión trial → paid
        $totalTrials = Subscription::where('status', 'trialing')->count();
        $convertedToPaid = Subscription::whereIn('status', ['active', 'past_due', 'suspended'])->count();
        $conversionRate = $totalTrials > 0
            ? round(($convertedToPaid / ($totalTrials + $convertedToPaid)) * 100, 1)
            : 0;

        // Churn rate: clínicas suspendidas o canceladas
        $churnedClinics = Subscription::whereIn('status', ['suspended', 'cancelled'])->count();
        $churnRate = $totalClinics > 0
            ? round(($churnedClinics / $totalClinics) * 100, 1)
            : 0;

        // Tasa de activación: % de clínicas con al menos 1 paciente
        $activatedClinics = Clinic::whereHas('patients')->count();
        $activationRate = $totalClinics > 0
            ? round(($activatedClinics / $totalClinics) * 100, 1)
            : 0;

        return [
            Stat::make('MRR', '$'.number_format($mrr, 2))
                ->description($mrrTrend >= 0 ? "+{$mrrTrend}% vs mes anterior" : "{$mrrTrend}% vs mes anterior")
                ->descriptionIcon($mrrTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($mrrTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Clínicas Activas', $activeClinics)
                ->description("de {$totalClinics} totales")
                ->descriptionIcon('heroicon-o-building-office')
                ->color('primary'),

            Stat::make('Conversión Trial', $conversionRate.'%')
                ->description("{$convertedToPaid} de ".($totalTrials + $convertedToPaid).' clínicas')
                ->descriptionIcon('heroicon-o-arrow-right-circle')
                ->color($conversionRate > 50 ? 'success' : 'warning'),

            Stat::make('Churn Rate', $churnRate.'%')
                ->description("{$churnedClinics} clínicas perdidas")
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color($churnRate < 10 ? 'success' : ($churnRate < 20 ? 'warning' : 'danger')),

            Stat::make('Activación', $activationRate.'%')
                ->description("{$activatedClinics} clínicas con pacientes")
                ->descriptionIcon('heroicon-o-check-circle')
                ->color($activationRate > 70 ? 'success' : 'warning'),
        ];
    }
}
