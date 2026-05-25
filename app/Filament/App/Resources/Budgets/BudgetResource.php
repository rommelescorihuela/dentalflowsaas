<?php

namespace App\Filament\App\Resources\Budgets;

use App\Filament\App\Resources\Budgets\Pages;
use App\Filament\App\Resources\Patients\PatientResource;
use App\Models\Budget;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Presupuestos';

    protected static ?string $navigationGroup = 'Finanzas';

    public static function getPluralModelLabel(): string
    {
        return 'Presupuestos';
    }

    public static function getModelLabel(): string
    {
        return 'Presupuesto';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('patient_id')
                    ->relationship('patient', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Borrador',
                        'sent' => 'Enviado',
                        'accepted' => 'Aceptado',
                        'rejected' => 'Rechazado',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('expires_at'),
                Forms\Components\Placeholder::make('odontogram_link')
                    ->label('Odontograma de Origen')
                    ->visible(fn(?Budget $record) => $record?->odontogram !== null)
                    ->content(fn(Budget $record) => view('filament.components.odontogram-link', ['odontogram' => $record->odontogram])),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3)
                    ->placeholder('Notas adicionales para el paciente...'),
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('procedure_price_id')
                            ->label('Procedimiento')
                            ->options(\App\Models\ProcedurePrice::pluck('procedure_name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $procedure = \App\Models\ProcedurePrice::find($state);
                                    if ($procedure) {
                                        $set('treatment_name', $procedure->procedure_name);
                                        $set('cost', $procedure->price);
                                    }
                                }
                            })
                            ->required(),
                        Forms\Components\Hidden::make('treatment_name'),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $cost = $get('cost') ?? 0;
                                $qty = $state ?? 1;
                                $set('subtotal', $cost * $qty);
                            })
                            ->required(),
                        Forms\Components\TextInput::make('cost')
                            ->numeric()
                            ->prefix('$')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $cost = $state ?? 0;
                                $qty = $get('quantity') ?? 1;
                                $set('subtotal', $cost * $qty);
                            })
                            ->required(),
                        Forms\Components\Placeholder::make('subtotal')
                            ->label('Subtotal')
                            ->content(function (callable $get) {
                                $cost = $get('cost') ?? 0;
                                $qty = $get('quantity') ?? 1;
                                return '$' . number_format($cost * $qty, 0, ',', '.');
                            }),
                    ])
                    ->columnSpanFull()
                    ->columns(5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('odontogram.name')
                    ->label('Origen')
                    ->placeholder('Manual')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('expires_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Borrador',
                        'sent' => 'Enviado',
                        'accepted' => 'Aceptado',
                        'rejected' => 'Rechazado',
                    ])
                    ->label('Estado'),
                Tables\Filters\Filter::make('expires_at')
                    ->form([
                        Forms\Components\DatePicker::make('expires_from')
                            ->label('Vence desde'),
                        Forms\Components\DatePicker::make('expires_until')
                            ->label('Vence hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['expires_from'] ?? null, fn($q) => $q->whereDate('expires_at', '>=', $data['expires_from']))
                            ->when($data['expires_until'] ?? null, fn($q) => $q->whereDate('expires_at', '<=', $data['expires_until']));
                    }),
                Tables\Filters\Filter::make('total')
                    ->form([
                        Forms\Components\TextInput::make('min_total')
                            ->label('Monto mínimo')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_total')
                            ->label('Monto máximo')
                            ->numeric(),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['min_total'] ?? null, fn($q) => $q->where('total', '>=', $data['min_total']))
                            ->when($data['max_total'] ?? null, fn($q) => $q->where('total', '<=', $data['max_total']));
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
        ];
    }
}
