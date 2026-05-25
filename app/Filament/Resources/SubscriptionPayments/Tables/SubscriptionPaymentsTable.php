<?php

namespace App\Filament\Resources\SubscriptionPayments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clinic.name')
                    ->label('Clínica')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('currency')
                    ->label('Moneda')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('method')
                    ->label('Método')
                    ->badge()
                    ->searchable()
                    ->formatStateUsing(fn(string $state): string => match($state) {
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'transfer' => 'Transferencia',
                        default => $state,
                    }),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match($state) {
                        'paid' => 'Pagado',
                        'pending' => 'Pendiente',
                        'failed' => 'Fallido',
                        default => ucfirst($state),
                    })
                    ->searchable(),
                TextColumn::make('transaction_id')
                    ->label('ID Transacción')
                    ->searchable(),
                TextColumn::make('paid_at')
                    ->label('Fecha de Pago')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha de Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
