<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentSystemActivities extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = '1/2';

    protected static ?string $heading = 'Actividad Reciente';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->with(['causer', 'clinic'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('causer.name')
                    ->label('Usuario')
                    ->searchable()
                    ->limit(20),
                TextColumn::make('event')
                    ->label('Accion')
                    ->badge()
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger' => 'deleted',
                        'gray' => 'restored',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Crear',
                        'updated' => 'Actualizar',
                        'deleted' => 'Eliminar',
                        'restored' => 'Restaurar',
                        default => ucfirst($state),
                    }),
                TextColumn::make('clinic.name')
                    ->label('Clinica')
                    ->placeholder('Central')
                    ->limit(15),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false)
            ->emptyStateHeading('Sin actividad registrada')
            ->emptyStateDescription('Las actividades del sistema apareceran aqui.')
            ->emptyStateIcon('heroicon-o-clock');
    }
}
