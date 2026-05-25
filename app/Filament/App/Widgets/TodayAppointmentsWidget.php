<?php

namespace App\Filament\App\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use App\Models\Appointment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;

class TodayAppointmentsWidget extends TableWidget
{
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Appointment::query()
                    ->whereDate('start_time', today())
                    ->with(['patient', 'procedurePrice'])
                    ->orderBy('start_time')
            )
            ->columns([
                TextColumn::make('start_time')
                    ->label('Hora')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('patient.name')
                    ->label('Paciente')
                    ->searchable()
                    ->limit(25),
                TextColumn::make('procedurePrice.procedure_name')
                    ->label('Procedimiento')
                    ->placeholder('Sin procedimiento')
                    ->limit(25),
                BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'primary' => 'scheduled',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                        'warning' => 'no-show',
                    ])
                    ->formatStateUsing(fn(string $state): string => match($state) {
                        'scheduled' => 'Programada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        'no-show' => 'No asistió',
                        default => $state,
                    }),
            ])
            ->paginated(false)
            ->emptyStateHeading('Sin citas para hoy')
            ->emptyStateDescription('Las citas programadas para hoy aparecerán aquí.')
            ->emptyStateIcon('heroicon-o-calendar');
    }

    protected function getHeader(): ?string
    {
        return 'Citas de Hoy';
    }
}
