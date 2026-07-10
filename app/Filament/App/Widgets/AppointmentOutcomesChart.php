<?php

namespace App\Filament\App\Widgets;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Services\PlanLimits;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AppointmentOutcomesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = '1/2';

    protected ?string $heading = 'Citas Completadas vs Canceladas';

    protected ?string $description = 'Últimos 12 meses';

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
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-m'));
        }

        $completed = Appointment::where('status', 'completed')
            ->where('start_time', '>=', now()->subYear())
            ->get()
            ->groupBy(fn ($a) => $a->start_time->format('Y-m'))
            ->map(fn ($group) => $group->count());

        $cancelled = Appointment::where('status', 'cancelled')
            ->where('start_time', '>=', now()->subYear())
            ->get()
            ->groupBy(fn ($a) => $a->start_time->format('Y-m'))
            ->map(fn ($group) => $group->count());

        $completedData = $months->map(fn ($m) => $completed->get($m, 0));
        $cancelledData = $months->map(fn ($m) => $cancelled->get($m, 0));

        return [
            'datasets' => [
                [
                    'label' => 'Completadas',
                    'data' => $completedData->values()->toArray(),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Canceladas',
                    'data' => $cancelledData->values()->toArray(),
                    'backgroundColor' => '#ef4444',
                    'borderColor' => '#dc2626',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $months->map(fn ($m) => Carbon::parse($m.'-01')->translatedFormat('M Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'stacked' => true,
                ],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
