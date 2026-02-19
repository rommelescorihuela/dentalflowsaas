<?php

namespace App\Filament\App\Resources\SystemActivities;

use App\Filament\App\Resources\SystemActivities\Pages;
use App\Models\SystemActivity;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;


class SystemActivityResource extends Resource
{
    protected static ?string $model = SystemActivity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Historial de Actividad';

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            Forms\Components\KeyValue::make('payload')
            ->label('Datos de la Petición')
            ->columnSpanFull(),
            Forms\Components\KeyValue::make('old_values')
            ->label('Valores Anteriores')
            ->columnSpanFull(),
            Forms\Components\KeyValue::make('new_values')
            ->label('Nuevos Valores')
            ->columnSpanFull(),
        ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('user.name')
            ->label('Usuario')
            ->searchable(),
            Tables\Columns\TextColumn::make('action')
            ->label('Acción')
            ->badge()
            ->colors([
                'success' => 'create',
                'warning' => 'update',
                'danger' => 'delete',
                'gray' => 'login',
            ])
            ->formatStateUsing(fn(string $state): string => match ($state) {
            'create' => 'Creación',
            'update' => 'Actualización',
            'delete' => 'Eliminación',
            'login' => 'Inicio de Sesión',
            default => ucfirst($state),
        }),
            Tables\Columns\TextColumn::make('subject_type')
            ->label('Entidad')
            ->formatStateUsing(fn($state) => class_basename($state))
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
            //
        ])
            ->actions([
            \Filament\Actions\ViewAction::make(),
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