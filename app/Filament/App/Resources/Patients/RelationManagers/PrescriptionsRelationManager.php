<?php

namespace App\Filament\App\Resources\Patients\RelationManagers;

use App\Models\Prescription;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PrescriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'prescriptions';

    protected static ?string $title = 'Recetas';

    protected static ?string $recordTitleAttribute = 'id';

    public function canCreate(): bool
    {
        return auth()->user()->can('create', Prescription::class);
    }

    public function canEdit($record): bool
    {
        return auth()->user()->can('update', $record);
    }

    public function canDelete($record): bool
    {
        return auth()->user()->can('delete', $record);
    }

    public function canView($record): bool
    {
        return auth()->user()->can('view', $record);
    }

    public function isReadOnly(): bool
    {
        $records = $this->getRelationship()->get();

        if ($records->isEmpty()) {
            return ! $this->canCreate();
        }

        return ! $this->canEdit($records->first());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Textarea::make('diagnosis')
                    ->label('Diagnóstico')
                    ->rows(2)
                    ->columnSpanFull(),
                Section::make('Medicamentos')
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
                                    ->numeric(),
                                Forms\Components\Textarea::make('indications')
                                    ->label('Indicaciones')
                                    ->rows(2),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->addActionLabel('Agregar medicamento'),
                    ]),
                Section::make('Firma Digital')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('digital_signature')
                            ->label('Firma del Doctor')
                            ->rows(2)
                            ->helperText('Escriba su nombre completo para firmar la receta'),
                        Forms\Components\DateTimePicker::make('signed_at')
                            ->label('Fecha de Firma')
                            ->default(now()),
                    ]),
                Section::make('Estado')
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Nueva Receta')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['clinic_id'] = auth()->user()->clinic_id;
                        $data['doctor_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
