<?php

namespace App\Filament\Widgets;

use App\Models\SubscriptionPayment;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SubscriptionRevenueChart extends ChartWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = '1/2';

    protected ?string $heading = 'Ingresos por Suscripciones';

    protected function getData(): array
    {
        $data = Trend::model(SubscriptionPayment::class)
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
                    'data' => $data->map(fn(TrendValue $value) => round($value->aggregate, 2)),
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#6366f1',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
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
                    'ticks' => [
                        'callback' => '(value) => "$" + value.toLocaleString("es-CL")',
                    ],
                ],
            ],
        ];
    }
}
