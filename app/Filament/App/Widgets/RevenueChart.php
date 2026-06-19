<?php

namespace App\Filament\App\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RevenueChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = '1/2';

    protected ?string $heading = 'Ingresos Mensuales';

    protected ?string $description = 'Últimos 12 meses';

    public static function canView(): bool
    {
        $tenant = tenant() ?? (\App\Models\Clinic::find(auth()->user()?->clinic_id));

        if (! $tenant) {
            return false;
        }

        return app(\App\Services\PlanLimits::class)->hasFeature($tenant, 'bi_reports');
    }

    protected function getData(): array
    {
        $data = Trend::model(Payment::class)
            ->dateColumn('paid_at')
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Ingresos',
                    'data' => $data->map(fn (TrendValue $value) => round($value->aggregate, 2)),
                    'borderColor' => '#06b6d4',
                    'backgroundColor' => 'rgba(6, 182, 212, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#06b6d4',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'borderWidth' => 3,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "$" + value.toLocaleString(); }',
                    ],
                ],
            ],
        ];
    }
}
