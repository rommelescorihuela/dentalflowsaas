<?php

namespace App\Filament\App\Resources\Budgets;

use App\Helpers\ClinicHelper;
use App\Models\Budget;
use App\Models\ProcedurePrice;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $navigationLabel = 'Presupuestos';

    protected static ?string $modelLabel = 'Presupuesto';

    protected static ?string $pluralModelLabel = 'Presupuestos';

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
                    ->prefix(ClinicHelper::getCurrencySymbol())
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
                    ->label('Odontograma Origen')
                    ->visible(fn (?Budget $record) => $record?->odontogram !== null)
                    ->content(fn (Budget $record) => view('filament.components.odontogram-link', ['odontogram' => $record->odontogram])),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3)
                    ->placeholder('Notas adicionales para el paciente...'),
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('procedure_price_id')
                            ->label('Procedimiento')
                            ->options(function () {
                                return ProcedurePrice::where('clinic_id', tenant('id'))
                                    ->pluck('procedure_name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $procedure = ProcedurePrice::find($state);
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
                            ->prefix(ClinicHelper::getCurrencySymbol())
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

                                return ClinicHelper::formatMoneyShort($cost * $qty);
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
                    ->formatStateUsing(fn ($state) => ClinicHelper::formatMoney((float) $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
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
                    ]),
            ])
            ->actions([
                Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->url(fn (Budget $record): string => route('budgets.pdf', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
