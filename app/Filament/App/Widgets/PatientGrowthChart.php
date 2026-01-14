<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\ChartWidget;

class PatientGrowthChart extends ChartWidget
{
    protected ?string $heading = 'Patient Growth Chart';

    protected function getData(): array
    {
        $data = \Flowframe\Trend\Trend::model(\App\Models\Patient::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'New Patients',
                    'data' => $data->map(fn(\Flowframe\Trend\TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(\Flowframe\Trend\TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
