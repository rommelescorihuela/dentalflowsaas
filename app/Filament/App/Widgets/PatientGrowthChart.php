<?php

namespace App\Filament\App\Widgets;

use App\Models\Clinic;
use App\Models\Patient;
use App\Services\PlanLimits;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class PatientGrowthChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = '1/2';

    protected ?string $heading = 'Nuevos Pacientes por Mes';

    public static function canView(): bool
    {
        $tenant = tenant() ?? (Clinic::find(auth()->user()?->clinic_id));

        if (! $tenant) {
            return false;
        }

        return app(PlanLimits::class)->hasFeature($tenant, 'bi_reports');
    }

    protected function getData(): array
    {
        $data = Trend::model(Patient::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Nuevos Pacientes',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#0891b2',
                    'backgroundColor' => 'rgba(8, 145, 178, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#0891b2',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
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
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
