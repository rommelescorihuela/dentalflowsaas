<?php

namespace App\Filament\Resources\SubscriptionPayments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SubscriptionPaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('clinic_id')
                    ->label('Clínica'),
                TextEntry::make('amount')
                    ->label('Monto')
                    ->numeric(),
                TextEntry::make('currency')
                    ->label('Moneda'),
                TextEntry::make('method')
                    ->label('Método'),
                TextEntry::make('status')
                    ->label('Estado'),
                TextEntry::make('transaction_id')
                    ->label('ID Transacción')
                    ->placeholder('-'),
                TextEntry::make('paid_at')
                    ->label('Fecha de Pago')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Fecha de Actualización')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
