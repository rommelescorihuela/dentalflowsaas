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
                    ->relationship('clinic', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('currency')
                    ->required()
                    ->default('USD'),
                \Filament\Forms\Components\Select::make('method')
                    ->options([
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'transfer' => 'Bank Transfer',
                    ])
                    ->required(),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ])
                    ->default('pending')
                    ->required(),
                TextInput::make('transaction_id'),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
