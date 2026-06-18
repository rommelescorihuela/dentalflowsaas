<?php

namespace App\Filament\Resources\SubscriptionPayments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubscriptionPaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('clinic_id')
                    ->label('Clínica')
                    ->relationship('clinic', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('subscription_id')
                    ->label('Suscripción')
                    ->relationship('subscription', 'plan')
                    ->searchable()
                    ->preload(),
                TextInput::make('amount')
                    ->label('Monto')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Select::make('currency')
                    ->label('Moneda')
                    ->options([
                        'USD' => 'USD',
                        'Bs' => 'Bolívares (Bs)',
                        'USDT' => 'USDT',
                    ])
                    ->required()
                    ->default('USD'),
                Select::make('method')
                    ->label('Método')
                    ->options([
                        'bank_transfer_bs' => 'Transferencia bancaria (Bs)',
                        'pago_movil_bs' => 'Pago móvil (Bs)',
                        'zelle_usd' => 'Zelle (USD)',
                        'binance_usdt' => 'Binance / USDT',
                    ])
                    ->required(),
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                    ])
                    ->default('pending')
                    ->required(),
                TextInput::make('reference')
                    ->label('Referencia / # operación'),
                TextInput::make('transaction_id')
                    ->label('ID Transacción'),
                DatePicker::make('period_start')
                    ->label('Inicio del período'),
                DatePicker::make('period_end')
                    ->label('Fin del período'),
                FileUpload::make('proof_path')
                    ->label('Comprobante')
                    ->disk('local')
                    ->directory('payments')
                    ->acceptedFileTypes(['image/*', 'application/pdf']),
                DateTimePicker::make('paid_at')
                    ->label('Pagado el'),
                DateTimePicker::make('verified_at')
                    ->label('Verificado el'),
            ]);
    }
}
