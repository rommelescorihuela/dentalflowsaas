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
    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Inventory::query()
                    ->whereColumn('current_stock', '<=', 'min_stock')
                    ->orderByRaw('(current_stock * 1.0 / NULLIF(min_stock, 0)) ASC')
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
                TextColumn::make('current_stock')
                    ->label('Stock')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('min_stock')
                    ->label('Mínimo')
                    ->alignCenter(),
                IconColumn::make('critical')
                    ->label('')
                    ->icon(fn($record) => $record->current_stock == 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-exclamation-circle')
                    ->color(fn($record) => $record->current_stock == 0 ? 'danger' : 'warning')
                    ->tooltip(fn($record) => $record->current_stock == 0 ? '¡Sin stock!' : 'Stock bajo'),
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
