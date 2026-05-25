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

    protected static ?string $navigationLabel = 'Pagos de Pacientes';

    public static function getNavigationGroup(): ?string
    {
        return 'Finanzas';
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
                    ->relationship('appointment', 'id')
                    ->searchable()
                    ->placeholder('Seleccionar Cita (Opcional)'),
                Select::make('budget_id')
                    ->relationship('budget', 'id')
                    ->searchable()
                    ->placeholder('Seleccionar Presupuesto (Opcional)'),
                TextInput::make('amount')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                Select::make('method')
                    ->options([
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'transfer' => 'Transferencia Bancaria',
                        'insurance' => 'Seguro',
                    ])
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pendiente',
                        'paid' => 'Pagado',
                        'refunded' => 'Reembolsado',
                    ])
                    ->default('paid')
                    ->required(),
                TextInput::make('reference_id')
                    ->label('ID de Referencia')
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pendiente',
                        'paid' => 'Pagado',
                        'refunded' => 'Reembolsado',
                    ])
                    ->label('Estado'),
                Tables\Filters\SelectFilter::make('method')
                    ->options([
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'transfer' => 'Transferencia',
                        'insurance' => 'Seguro',
                    ])
                    ->label('Método'),
                Tables\Filters\Filter::make('paid_at')
                    ->form([
                        DatePicker::make('paid_from')
                            ->label('Desde'),
                        DatePicker::make('paid_until')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['paid_from'] ?? null, fn($q) => $q->whereDate('paid_at', '>=', $data['paid_from']))
                            ->when($data['paid_until'] ?? null, fn($q) => $q->whereDate('paid_at', '<=', $data['paid_until']));
                    }),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
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
