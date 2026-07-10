<?php

namespace App\Filament\Widgets;

use App\Models\SystemActivity;
use Filament\Tables\Columns\BadgeColumn;
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
                SystemActivity::query()
                    ->with('user', 'clinic')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->limit(20),
                BadgeColumn::make('action')
                    ->label('Accion')
                    ->colors([
                        'success' => 'create',
                        'info' => 'update',
                        'danger' => 'delete',
                        'warning' => 'login',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'create' => 'Crear',
                        'update' => 'Actualizar',
                        'delete' => 'Eliminar',
                        'login' => 'Inicio Sesion',
                        default => $state,
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
