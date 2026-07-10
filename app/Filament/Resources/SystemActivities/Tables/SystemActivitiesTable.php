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
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('action')
                    ->label('Accion')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('subject_type')
                    ->label('Asunto')
                    ->formatStateUsing(fn ($state) => class_basename($state))
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
                SelectFilter::make('action')
                    ->options([
                        'create' => 'Crear',
                        'update' => 'Actualizar',
                        'delete' => 'Eliminar',
                        'login' => 'Iniciar Sesión',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk delete for audit logs mostly
            ]);
    }
}
