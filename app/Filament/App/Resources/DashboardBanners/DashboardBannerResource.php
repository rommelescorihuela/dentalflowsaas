<?php

namespace App\Filament\App\Resources\DashboardBanners;

use App\Models\DashboardBanner;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DashboardBannerResource extends Resource
{
    protected static ?string $model = DashboardBanner::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-speaker-wave';

    protected static ?string $navigationLabel = 'Banners del Dashboard';

    protected static ?string $modelLabel = 'Banner';

    protected static ?string $pluralModelLabel = 'Banners';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required(),
                Forms\Components\Textarea::make('message')
                    ->label('Mensaje')
                    ->columnSpanFull(),
                Forms\Components\Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'info' => 'Información',
                        'success' => 'Éxito',
                        'warning' => 'Advertencia',
                        'error' => 'Error',
                        'promo' => 'Promoción',
                    ])
                    ->required(),
                Forms\Components\Select::make('color')
                    ->label('Color')
                    ->options([
                        'blue' => 'Azul',
                        'green' => 'Verde',
                        'yellow' => 'Amarillo',
                        'red' => 'Rojo',
                        'purple' => 'Púrpura',
                        'cyan' => 'Cian',
                    ])
                    ->required(),
                Forms\Components\Select::make('icon')
                    ->label('Ícono')
                    ->options([
                        'heroicon-o-information-circle' => 'Información',
                        'heroicon-o-check-circle' => 'Check',
                        'heroicon-o-exclamation-triangle' => 'Advertencia',
                        'heroicon-o-x-circle' => 'Error',
                        'heroicon-o-sparkles' => 'Promoción',
                        'heroicon-o-bell' => 'Campana',
                        'heroicon-o-light-bulb' => 'Idea',
                        'heroicon-o-calendar' => 'Calendario',
                    ])
                    ->nullable(),
                Forms\Components\TextInput::make('link')
                    ->label('Enlace')
                    ->url(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),
                Forms\Components\DateTimePicker::make('starts_at')
                    ->label('Inicia'),
                Forms\Components\DateTimePicker::make('ends_at')
                    ->label('Termina'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'info' => 'blue',
                        'success' => 'green',
                        'warning' => 'yellow',
                        'error' => 'red',
                        'promo' => 'purple',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('color')
                    ->label('Color')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDashboardBanners::route('/'),
            'create' => Pages\CreateDashboardBanner::route('/create'),
            'edit' => Pages\EditDashboardBanner::route('/{record}/edit'),
        ];
    }
}
