<?php

namespace App\Filament\Widgets;

use App\Enums\Plan;
use App\Models\Clinic;
use App\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // MRR = ingreso recurrente de las suscripciones activas según su plan.
        // (Antes se sumaban los pagos aprobados del mes: un mes sin cobros
        // aprobados mostraba $0 y -100 %, aunque hubiera clientes de pago.)
        $suscripcionesActivas = Subscription::where('status', 'active')->get();

        $mrr = $suscripcionesActivas->sum(function (Subscription $suscripcion): float {
            $plan = $suscripcion->plan;
            $clave = $plan instanceof Plan ? $plan->value : (string) $plan;

            return (float) (config("plans.{$clave}.price_usd") ?? 0);
        });

        $arpu = $suscripcionesActivas->count() > 0
            ? round($mrr / $suscripcionesActivas->count(), 2)
            : 0;

        // Clínicas en prueba y la que vence antes
        $enPrueba = Subscription::where('status', 'trialing')->count();
        $proximoVencimiento = Subscription::where('status', 'trialing')
            ->whereNotNull('trial_ends_at')
            ->orderBy('trial_ends_at')
            ->first();

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
                ->description($suscripcionesActivas->count().' clínicas de pago · ARPU $'.number_format($arpu, 0))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($mrr > 0 ? 'success' : 'gray'),

            Stat::make('Clínicas Activas', $activeClinics)
                ->description("de {$totalClinics} totales")
                ->descriptionIcon('heroicon-o-building-office')
                ->color('primary'),

            Stat::make('Conversión Trial', $conversionRate.'%')
                ->description("{$convertedToPaid} de ".($totalTrials + $convertedToPaid).' clínicas')
                ->descriptionIcon('heroicon-o-arrow-right-circle')
                ->color($conversionRate > 50 ? 'success' : 'warning'),

            Stat::make('Tasa de Cancelación', $churnRate.'%')
                ->description("{$churnedClinics} clínicas perdidas")
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color($churnRate < 10 ? 'success' : ($churnRate < 20 ? 'warning' : 'danger')),

            Stat::make('Activación', $activationRate.'%')
                ->description("{$activatedClinics} clínicas con pacientes")
                ->descriptionIcon('heroicon-o-check-circle')
                ->color($activationRate > 70 ? 'success' : 'warning'),

            Stat::make('En prueba', (string) $enPrueba)
                ->description($proximoVencimiento
                    ? 'La próxima vence el '.$proximoVencimiento->trial_ends_at->format('d/m/Y')
                    : 'Sin pruebas activas')
                ->descriptionIcon('heroicon-o-clock')
                ->color($enPrueba > 0 ? 'info' : 'gray'),
        ];
    }
}
