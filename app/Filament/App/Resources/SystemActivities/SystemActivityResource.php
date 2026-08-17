<?php

namespace App\Filament\App\Resources\SystemActivities;

use App\Models\Activity;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SystemActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Historial de Actividad';

    protected static ?string $recordTitleAttribute = 'description';

    public static function getPluralModelLabel(): string
    {
        return 'Actividades';
    }

    public static function getModelLabel(): string
    {
        return 'Actividad';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\KeyValue::make('properties.attributes')
                    ->label('Nuevos Valores')
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('properties.old')
                    ->label('Valores Anteriores')
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('properties.payload')
                    ->label('Datos de la Petición')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Usuario')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event')
                    ->label('Acción')
                    ->badge()
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
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Entidad')
                    ->formatStateUsing(fn ($state) => class_basename((string) $state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->options([
                        'created' => 'Creación',
                        'updated' => 'Actualización',
                        'deleted' => 'Eliminación',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('clinic_id', tenant('id'));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystemActivities::route('/'),
            'view' => Pages\ViewSystemActivity::route('/{record}'),
        ];
    }
}
