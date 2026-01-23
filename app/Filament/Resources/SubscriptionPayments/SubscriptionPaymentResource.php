<?php

namespace App\Filament\Resources\SubscriptionPayments;

use App\Filament\Resources\SubscriptionPayments\Pages\CreateSubscriptionPayment;
use App\Filament\Resources\SubscriptionPayments\Pages\EditSubscriptionPayment;
use App\Filament\Resources\SubscriptionPayments\Pages\ListSubscriptionPayments;
use App\Filament\Resources\SubscriptionPayments\Pages\ViewSubscriptionPayment;
use App\Filament\Resources\SubscriptionPayments\Schemas\SubscriptionPaymentForm;
use App\Filament\Resources\SubscriptionPayments\Schemas\SubscriptionPaymentInfolist;
use App\Filament\Resources\SubscriptionPayments\Tables\SubscriptionPaymentsTable;
use App\Models\SubscriptionPayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubscriptionPaymentResource extends Resource
{
    protected static ?string $model = SubscriptionPayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'transaction_id';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationLabel = 'Subscription Payments';

    public static function form(Schema $schema): Schema
    {
        return SubscriptionPaymentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubscriptionPaymentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubscriptionPaymentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscriptionPayments::route('/'),
            'create' => CreateSubscriptionPayment::route('/create'),
            'view' => ViewSubscriptionPayment::route('/{record}'),
            'edit' => EditSubscriptionPayment::route('/{record}/edit'),
        ];
    }
}
