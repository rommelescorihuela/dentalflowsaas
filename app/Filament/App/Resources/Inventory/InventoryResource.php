<?php

namespace App\Filament\App\Resources\Inventory;

use App\Filament\App\Resources\Inventory\Pages;
use App\Models\Inventory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $navigationLabel = 'Inventario';

    public static function getNavigationGroup(): ?string
    {
        return 'Gestión de Clínica';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Inventario';
    }

    public static function getModelLabel(): string
    {
        return 'Producto';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Select::make('category')
                    ->label('Categoría')
                    ->options([
                        'Consumables' => 'Consumibles',
                        'Instruments' => 'Instrumentos',
                        'Equipment' => 'Equipos',
                        'Other' => 'Otros',
                    ])
                    ->required(),
                TextInput::make('supplier')
                    ->label('Proveedor')
                    ->required()
                    ->maxLength(255),
                TextInput::make('price')
                    ->label('Precio')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                TextInput::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->required(),
                TextInput::make('low_stock_threshold')
                    ->label('Umbral de Stock Bajo')
                    ->numeric()
                    ->default(10)
                    ->required(),
                TextInput::make('unit')
                    ->label('Unidad')
                    ->default('piezas')
                    ->required(),
                TextInput::make('items_per_unit')
                    ->label('Ítems por Unidad')
                    ->numeric()
                    ->default(1)
                    ->required(),
                Select::make('expiration_type')
                    ->label('Tipo de Vencimiento')
                    ->options([
                        'Expirable' => 'Con Vencimiento',
                        'Inexpirable' => 'Sin Vencimiento',
                    ])
                    ->default('Expirable')
                    ->reactive()
                    ->required(),
                DatePicker::make('expiration_date')
                    ->label('Fecha de Vencimiento')
                    ->hidden(fn($get) => $get('expiration_type') === 'Inexpirable'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Categoría')
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => match($state) {
                        'Consumables' => 'Consumibles',
                        'Instruments' => 'Instrumentos',
                        'Equipment' => 'Equipos',
                        'Other' => 'Otros',
                        default => $state,
                    }),
                TextColumn::make('supplier')
                    ->label('Proveedor')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->sortable()
                    ->color(fn(Inventory $record) => $record->quantity <= $record->low_stock_threshold ? 'danger' : 'success'),
                TextColumn::make('price')
                    ->label('Precio')
                    ->money('USD'),
                TextColumn::make('expiration_date')
                    ->label('Fecha de Vencimiento')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventories::route('/'),
            'create' => Pages\CreateInventory::route('/create'),
            'edit' => Pages\EditInventory::route('/{record}/edit'),
            'view' => Pages\ViewInventory::route('/{record}'),
        ];
    }
}
