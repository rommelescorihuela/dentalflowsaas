<?php

namespace App\Filament\App\Resources\Patients\RelationManagers;

use App\Models\PatientMedicalHistory;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ClinicalHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'medicalHistory';

    protected static ?string $title = 'Historia Clínica';

    protected static ?string $recordTitleAttribute = 'id';

    public function canCreate(): bool
    {
        return auth()->user()->can('create', PatientMedicalHistory::class);
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
        $record = $this->getRelationship()->first();

        if (! $record) {
            return ! $this->canCreate();
        }

        return ! $this->canEdit($record);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Antecedentes')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('antecedentes_personales')
                            ->label('Antecedentes Personales')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('antecedentes_familiares')
                            ->label('Antecedentes Familiares')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('alergias')
                            ->label('Alergias')
                            ->rows(2),
                        Forms\Components\Textarea::make('medicamentos_actuales')
                            ->label('Medicamentos Actuales')
                            ->rows(2),
                        Forms\Components\Textarea::make('enfermedades_cronicas')
                            ->label('Enfermedades Crónicas')
                            ->rows(2),
                    ]),
                Section::make('Hábitos y Cirugías')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('habitos')
                            ->label('Hábitos')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('cirugias_previas')
                            ->label('Cirugías Previas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Historia Dental')
                    ->schema([
                        Forms\Components\Textarea::make('historia_dental')
                            ->label('Historia Dental')
                            ->rows(3),
                    ]),
                Section::make('Signos Vitales')
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('presion_arterial')
                            ->label('Presión Arterial')
                            ->placeholder('120/80'),
                        Forms\Components\TextInput::make('frecuencia_cardiaca')
                            ->label('Frecuencia Cardíaca')
                            ->placeholder('72')
                            ->numeric(),
                        Forms\Components\TextInput::make('peso')
                            ->label('Peso (kg)')
                            ->numeric()
                            ->step(0.1),
                        Forms\Components\TextInput::make('altura')
                            ->label('Altura (m)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\Select::make('grupo_sanguineo')
                            ->label('Grupo Sanguíneo')
                            ->options([
                                'A+' => 'A+', 'A-' => 'A-',
                                'B+' => 'B+', 'B-' => 'B-',
                                'AB+' => 'AB+', 'AB-' => 'AB-',
                                'O+' => 'O+', 'O-' => 'O-',
                            ])
                            ->native(false),
                    ]),
                Section::make('Consentimiento')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('firma_paciente')
                            ->label('Firma del Paciente')
                            ->rows(2)
                            ->helperText('Nombre completo como constancia de firma'),
                        Forms\Components\DateTimePicker::make('fecha_firma')
                            ->label('Fecha de Firma'),
                        Forms\Components\TextInput::make('nombre_testigo')
                            ->label('Nombre del Testigo')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('rut_testigo')
                            ->label('RUT / DNI del Testigo')
                            ->maxLength(20),
                    ]),
                Section::make('Observaciones')
                    ->schema([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(2),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('grupo_sanguineo')
                    ->label('Grupo Sanguíneo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'A+', 'A-' => 'danger',
                        'B+', 'B-' => 'warning',
                        'AB+', 'AB-' => 'info',
                        'O+', 'O-' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('presion_arterial')
                    ->label('Presión Arterial'),
                Tables\Columns\TextColumn::make('peso')
                    ->label('Peso')
                    ->suffix(' kg'),
                Tables\Columns\TextColumn::make('altura')
                    ->label('Altura')
                    ->suffix(' m'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nueva Historia Clínica')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['clinic_id'] = auth()->user()->clinic_id;

                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}
