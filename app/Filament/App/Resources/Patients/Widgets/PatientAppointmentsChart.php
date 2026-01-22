<?php

namespace App\Filament\App\Resources\Patients\Widgets;

use App\Models\Patient;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Appointment;

class PatientAppointmentsChart extends ChartWidget
{
    public ?Patient $record = null;

    protected ?string $heading = 'Appointments over time';

    protected function getData(): array
    {
        if (!$this->record) {
            return [];
        }

        $data = Trend::query($this->record->appointments())
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Appointments',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
