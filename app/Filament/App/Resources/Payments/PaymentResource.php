<?php

namespace App\Filament\App\Resources\Payments;

use App\Filament\App\Resources\Payments\Pages;
use App\Models\Payment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Patient Payments';

    public static function getNavigationGroup(): ?string
    {
        return 'Finance';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('patient_id')
                    ->relationship('patient', 'name')
                    ->searchable()
                    ->required(),
                Select::make('appointment_id')
                    ->relationship('appointment', 'id') // Ideally should show date/type
                    ->searchable()
                    ->placeholder('Select Appointment (Optional)'),
                Select::make('budget_id')
                    ->relationship('budget', 'id') // Ideally should show total/date
                    ->searchable()
                    ->placeholder('Select Budget (Optional)'),
                TextInput::make('amount')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                Select::make('method')
                    ->options([
                        'cash' => 'Cash',
                        'card' => 'Card',
                        'transfer' => 'Bank Transfer',
                        'insurance' => 'Insurance',
                    ])
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'refunded' => 'Refunded',
                    ])
                    ->default('paid')
                    ->required(),
                TextInput::make('reference_id')
                    ->label('Reference ID')
                    ->maxLength(255),
                DateTimePicker::make('paid_at')
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.name')->searchable()->sortable(),
                TextColumn::make('amount')->money('USD')->sortable(),
                TextColumn::make('method')->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'refunded' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('paid_at')->dateTime()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
}
