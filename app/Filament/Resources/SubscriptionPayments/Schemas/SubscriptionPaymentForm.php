<?php

namespace App\Filament\Resources\SubscriptionPayments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubscriptionPaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('clinic_id')
                    ->label('Clínica')
                    ->relationship('clinic', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('amount')
                    ->label('Monto')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('currency')
                    ->label('Moneda')
                    ->required()
                    ->default('USD'),
                \Filament\Forms\Components\Select::make('method')
                    ->label('Método')
                    ->options([
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'transfer' => 'Transferencia Bancaria',
                    ])
                    ->required(),
                \Filament\Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'paid' => 'Pagado',
                        'failed' => 'Fallido',
                    ])
                    ->default('pending')
                    ->required(),
                TextInput::make('transaction_id')
                    ->label('ID de Transacción'),
                DateTimePicker::make('paid_at')
                    ->label('Fecha de Pago'),
            ]);
    }
}
