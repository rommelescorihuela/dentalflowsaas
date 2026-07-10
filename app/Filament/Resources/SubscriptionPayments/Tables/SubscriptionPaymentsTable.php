<?php

namespace App\Filament\Resources\SubscriptionPayments\Tables;

use App\Enums\Plan;
use App\Services\SubscriptionService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubscriptionPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bank_transfer_bs' => 'Transf. bancaria',
                        'pago_movil_bs' => 'Pago móvil',
                        'zelle_usd' => 'Zelle',
                        'binance_usdt' => 'Binance/USDT',
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'transfer' => 'Transferencia',
                        default => $state,
                    }),
                TextColumn::make('reference')
                    ->label('Referencia')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        'paid' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approved' => 'Aprobado',
                        'pending' => 'Pendiente',
                        'rejected' => 'Rechazado',
                        'paid' => 'Pagado',
                        'failed' => 'Fallido',
                        default => ucfirst($state),
                    })
                    ->searchable(),
                TextColumn::make('proof_path')
                    ->label('Comprobante')
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => $state ? 'Sí' : '—'),
                TextColumn::make('verifier.name')
                    ->label('Verificado por')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('paid_at')
                    ->label('Fecha de Pago')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                    ]),
                SelectFilter::make('method')
                    ->label('Método')
                    ->options([
                        'bank_transfer_bs' => 'Transferencia bancaria (Bs)',
                        'pago_movil_bs' => 'Pago móvil (Bs)',
                        'zelle_usd' => 'Zelle (USD)',
                        'binance_usdt' => 'Binance / USDT',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->form([
                        Select::make('plan')
                            ->label('Plan a activar')
                            ->options(collect([Plan::Basic, Plan::Pro])->mapWithKeys(fn (Plan $p) => [$p->value => $p->label().' — $'.number_format($p->priceUsd(), 0).'/mes'])->toArray())
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $clinic = $record->clinic;
                        $service = app(SubscriptionService::class);

                        if ($clinic->subscription && $clinic->subscription->status->value === 'active') {
                            $service->extend($clinic, $record);
                        } else {
                            $service->activate($clinic, Plan::from($data['plan']), $record);
                        }

                        Notification::make()
                            ->title('Pago aprobado')
                            ->body('La suscripción ha sido activada/extendida.')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update(['status' => 'rejected']);

                        Notification::make()
                            ->title('Pago rechazado')
                            ->warning()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
