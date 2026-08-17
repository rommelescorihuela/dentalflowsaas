<?php

namespace App\Filament\Resources\SystemActivities\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SystemActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('causer.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('event')
                    ->label('Accion')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger' => 'deleted',
                        'gray' => 'restored',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Creación',
                        'updated' => 'Actualización',
                        'deleted' => 'Eliminación',
                        'restored' => 'Restauración',
                        default => ucfirst($state),
                    }),
                TextColumn::make('subject_type')
                    ->label('Asunto')
                    ->formatStateUsing(fn ($state) => class_basename((string) $state))
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Descripcion')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->options([
                        'created' => 'Creación',
                        'updated' => 'Actualización',
                        'deleted' => 'Eliminación',
                        'restored' => 'Restauración',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk delete for audit logs
            ]);
    }
}
