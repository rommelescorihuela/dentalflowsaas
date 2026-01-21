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

    public static function getNavigationGroup(): ?string
    {
        return 'Clinic Management';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('category')
                    ->options([
                        'Consumables' => 'Consumables',
                        'Instruments' => 'Instruments',
                        'Equipment' => 'Equipment',
                        'Other' => 'Other',
                    ])
                    ->required(),
                TextInput::make('supplier')
                    ->required()
                    ->maxLength(255),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                TextInput::make('quantity')
                    ->numeric()
                    ->required(),
                TextInput::make('low_stock_threshold')
                    ->numeric()
                    ->default(10)
                    ->required(),
                TextInput::make('unit')
                    ->default('pieces')
                    ->required(),
                TextInput::make('items_per_unit')
                    ->numeric()
                    ->default(1)
                    ->required(),
                Select::make('expiration_type')
                    ->options([
                        'Expirable' => 'Expirable',
                        'Inexpirable' => 'Inexpirable',
                    ])
                    ->default('Expirable')
                    ->reactive()
                    ->required(),
                DatePicker::make('expiration_date')
                    ->hidden(fn($get) => $get('expiration_type') === 'Inexpirable'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category')->sortable(),
                TextColumn::make('supplier')->searchable(),
                TextColumn::make('quantity')->sortable()
                    ->color(fn(Inventory $record) => $record->quantity <= $record->low_stock_threshold ? 'danger' : 'success'),
                TextColumn::make('price')->money('USD'),
                TextColumn::make('expiration_date')->date(),
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
