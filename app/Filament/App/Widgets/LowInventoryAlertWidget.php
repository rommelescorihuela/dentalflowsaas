<?php

namespace App\Filament\App\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use App\Models\Inventory;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;

class LowInventoryAlertWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = '1/2';

    protected static ?string $heading = 'Alertas de Inventario';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Inventory::query()
                    ->whereColumn('quantity', '<=', 'low_stock_threshold')
                    ->orderByRaw('(quantity * 1.0 / NULLIF(low_stock_threshold, 0)) ASC')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('quantity')
                    ->label('Stock')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('low_stock_threshold')
                    ->label('Mínimo')
                    ->alignCenter(),
                IconColumn::make('critical')
                    ->label('')
                    ->icon(fn($record) => $record->quantity == 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-exclamation-circle')
                    ->color(fn($record) => $record->quantity == 0 ? 'danger' : 'warning')
                    ->tooltip(fn($record) => $record->quantity == 0 ? '¡Sin stock!' : 'Stock bajo'),
            ])
            ->paginated(false)
            ->emptyStateHeading('Todo el inventario está bien')
            ->emptyStateDescription('No hay productos con stock bajo.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    protected function getHeader(): ?string
    {
        return 'Alertas de Inventario';
    }
}
