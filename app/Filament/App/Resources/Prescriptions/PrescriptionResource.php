<?php

namespace App\Filament\App\Resources\Prescriptions;

use App\Models\Prescription;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PrescriptionResource extends Resource
{
    protected static ?string $model = Prescription::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Recetas';

    protected static ?string $modelLabel = 'Receta';

    protected static ?string $pluralModelLabel = 'Recetas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('patient_id')
                    ->label('Paciente')
                    ->relationship('patient', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Textarea::make('diagnosis')
                    ->label('Diagnóstico')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Section::make('Medicamentos')
                    ->description('Lista de medicamentos recetados')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('medication')
                                    ->label('Medicamento')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('dosage')
                                    ->label('Dosis')
                                    ->placeholder('500mg')
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('frequency')
                                    ->label('Frecuencia')
                                    ->placeholder('Cada 8 horas')
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('duration')
                                    ->label('Duración')
                                    ->placeholder('7 días')
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->placeholder('30')
                                    ->numeric(),
                                Forms\Components\Textarea::make('indications')
                                    ->label('Indicaciones')
                                    ->placeholder('Tomar después de cada comida...')
                                    ->rows(2),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->addActionLabel('Agregar medicamento'),
                    ]),
                Forms\Components\Section::make('Firma Digital')
                    ->description('Firma del doctor que receta')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('digital_signature')
                            ->label('Firma del Doctor')
                            ->placeholder('Nombre completo del doctor como constancia de firma')
                            ->rows(2)
                            ->helperText('Escriba su nombre completo para firmar la receta'),
                        Forms\Components\DateTimePicker::make('signed_at')
                            ->label('Fecha de Firma')
                            ->default(now()),
                    ]),
                Forms\Components\Section::make('Estado y Notas')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activa',
                                'completed' => 'Completada',
                                'cancelled' => 'Cancelada',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('Doctor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Medicamentos')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activa',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('signed_at')
                    ->label('Firmada')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activa',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrescriptions::route('/'),
            'create' => Pages\CreatePrescription::route('/create'),
            'edit' => Pages\EditPrescription::route('/{record}/edit'),
        ];
    }
}
