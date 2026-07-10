<?php

namespace App\Filament\App\Resources\ProcedurePrices;

use App\Helpers\ClinicHelper;
use App\Models\ProcedurePrice;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProcedurePriceResource extends Resource
{
    protected static ?string $model = ProcedurePrice::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Precios de Procedimientos';

    protected static ?string $modelLabel = 'Precio de Procedimiento';

    protected static ?string $pluralModelLabel = 'Precios de Procedimientos';

    public static function getNavigationGroup(): ?string
    {
        return 'Gestión de Clínica';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('procedure_name')
                    ->label('Nombre del Procedimiento')
                    ->required()
                    ->maxLength(255),
                TextInput::make('price')
                    ->numeric()
                    ->prefix(ClinicHelper::getCurrencySymbol())
                    ->required(),
                TextInput::make('duration')
                    ->label('Duración')
                    ->placeholder('ej: 30 minutos')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Imagen')
                    ->image()
                    ->directory('procedure-images')
                    ->columnSpanFull(),
                Repeater::make('procedureInventories')
                    ->relationship()
                    ->schema([
                        Select::make('inventory_id')
                            ->relationship('inventory', 'name')
                            ->required()
                            ->searchable()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                        TextInput::make('quantity_used')
                            ->numeric()
                            ->required()
                            ->label('Cantidad a Deducir')
                            ->default(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->label('Items de Inventario Vinculados'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Imagen')
                    ->circular(),
                TextColumn::make('procedure_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => ClinicHelper::formatMoney((float) $state))
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
