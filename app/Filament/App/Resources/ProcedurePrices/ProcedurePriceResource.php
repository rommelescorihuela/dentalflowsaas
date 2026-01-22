<?php

namespace App\Filament\App\Resources\ProcedurePrices;

use App\Filament\App\Resources\ProcedurePrices\Pages;
use App\Models\ProcedurePrice;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class ProcedurePriceResource extends Resource
{
    protected static ?string $model = ProcedurePrice::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    public static function getNavigationGroup(): ?string
    {
        return 'Clinic Management';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('procedure_name')
                    ->label('Procedure Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                TextInput::make('duration')
                    ->label('Duration')
                    ->placeholder('e.g. 30 minutes')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Image')
                    ->image()
                    ->directory('procedure-images')
                    ->columnSpanFull(),
                \Filament\Forms\Components\Repeater::make('procedureInventories')
                    ->relationship()
                    ->schema([
                        \Filament\Forms\Components\Select::make('inventory_id')
                            ->relationship('inventory', 'name')
                            ->required()
                            ->searchable()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                        TextInput::make('quantity_used')
                            ->numeric()
                            ->required()
                            ->label('Quantity to Deduct')
                            ->default(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->label('Linked Inventory Items'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Image')
                    ->circular(),
                TextColumn::make('procedure_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('duration'),
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
            'index' => Pages\ListProcedurePrices::route('/'),
            'create' => Pages\CreateProcedurePrice::route('/create'),
            'edit' => Pages\EditProcedurePrice::route('/{record}/edit'),
            'view' => Pages\ViewProcedurePrice::route('/{record}'),
        ];
    }
}
