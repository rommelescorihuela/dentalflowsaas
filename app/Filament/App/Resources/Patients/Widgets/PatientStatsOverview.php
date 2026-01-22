<?php

namespace App\Filament\App\Resources\Patients\Widgets;

use App\Models\Patient;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Appointment;

class PatientStatsOverview extends BaseWidget
{
    public ?Patient $record = null;

    protected function getStats(): array
    {
        if (!$this->record) {
            return [];
        }

        $appointments = $this->record->appointments();

        return [
            Stat::make('Total Appointments', $appointments->count())
                ->icon('heroicon-o-calendar'),

            Stat::make('Completed Treatments', $appointments->where('status', 'completed')->count())
                ->description('Fully processed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Pending/Scheduled', $appointments->whereIn('status', ['scheduled', 'confirmed'])->count())
                ->description('Upcoming')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
