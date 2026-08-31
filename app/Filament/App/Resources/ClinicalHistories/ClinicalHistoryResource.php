<?php

namespace App\Filament\App\Resources\ClinicalHistories;

use App\Models\PatientMedicalHistory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ClinicalHistoryResource extends Resource
{
    protected static ?string $model = PatientMedicalHistory::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Historias Clínicas';

    protected static ?string $modelLabel = 'Historia Clínica';

    protected static ?string $pluralModelLabel = 'Historias Clínicas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('patient_id')
                    ->label('Paciente')
                    ->relationship('patient', 'name')
                    ->required()
                    ->searchable()
                    ->disabled(fn ($operation) => $operation === 'edit'),
                Section::make('Antecedentes')
                    ->description('Antecedentes personales, familiares y alergias')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('antecedentes_personales')
                            ->label('Antecedentes Personales')
                            ->placeholder('Enfermedades previas, hospitalizaciones, transfusiones...')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('antecedentes_familiares')
                            ->label('Antecedentes Familiares')
                            ->placeholder('Diabetes, hipertensión, cáncer, enfermedades hereditarias...')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('alergias')
                            ->label('Alergias')
                            ->placeholder('Medicamentos, alimentos, látex, anestésicos...')
                            ->rows(3),
                        Forms\Components\Textarea::make('medicamentos_actuales')
                            ->label('Medicamentos Actuales')
                            ->placeholder('Anticoagulantes, antihipertensivos, insulina...')
                            ->rows(3),
                        Forms\Components\Textarea::make('enfermedades_cronicas')
                            ->label('Enfermedades Crónicas')
                            ->placeholder('Diabetes, hipertensión, asma, epilepsia...')
                            ->rows(3),
                    ]),
                Section::make('Hábitos y Cirugías')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('habitos')
                            ->label('Hábitos')
                            ->placeholder('Tabaquismo, alcohol, drogas, bruxismo, dieta...')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('cirugias_previas')
                            ->label('Cirugías Previas')
                            ->placeholder('Cirugías maxilofaciales, extracciones, implantes...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
                Section::make('Historia Dental')
                    ->schema([
                        Forms\Components\Textarea::make('historia_dental')
                            ->label('Historia Dental')
                            ->placeholder('Tratamientos previos, frecuencia de visitas, higiene oral...')
                            ->rows(4),
                    ]),
                Section::make('Signos Vitales')
                    ->description('Registro de signos vitales actuales')
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('presion_arterial')
                            ->label('Presión Arterial')
                            ->placeholder('120/80')
                            ->helperText('mmHg'),
                        Forms\Components\TextInput::make('frecuencia_cardiaca')
                            ->label('Frecuencia Cardíaca')
                            ->placeholder('72')
                            ->helperText('lpm')
                            ->numeric(),
                        Forms\Components\TextInput::make('peso')
                            ->label('Peso')
                            ->placeholder('70')
                            ->helperText('kg')
                            ->numeric()
                            ->step(0.1),
                        Forms\Components\TextInput::make('altura')
                            ->label('Altura')
                            ->placeholder('1.70')
                            ->helperText('m')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\Select::make('grupo_sanguineo')
                            ->label('Grupo Sanguíneo')
                            ->options([
                                'A+' => 'A+',
                                'A-' => 'A-',
                                'B+' => 'B+',
                                'B-' => 'B-',
                                'AB+' => 'AB+',
                                'AB-' => 'AB-',
                                'O+' => 'O+',
                                'O-' => 'O-',
                            ])
                            ->native(false),
                    ]),
                Section::make('Consentimiento y Testigos')
                    ->description('Firma digital del paciente y datos del testigo')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('firma_paciente')
                            ->label('Firma del Paciente')
                            ->placeholder('Nombre completo del paciente (firma digital)')
                            ->rows(2)
                            ->helperText('Escriba el nombre completo como constancia de firma'),
                        Forms\Components\DateTimePicker::make('fecha_firma')
                            ->label('Fecha de Firma')
                            ->default(now()),
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
                            ->label('Observaciones Generales')
                            ->rows(3),
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
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListClinicalHistories::route('/'),
            'create' => Pages\CreateClinicalHistory::route('/create'),
            'edit' => Pages\EditClinicalHistory::route('/{record}/edit'),
        ];
    }
}
