<?php

namespace App\Filament\App\Resources\Patients;

use App\Models\Patient;
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
use Illuminate\Support\Facades\URL;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pacientes';

    protected static ?string $modelLabel = 'Paciente';

    protected static ?string $pluralModelLabel = 'Pacientes';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefono')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('rut')
                    ->label('RUT / DNI')
                    ->maxLength(20),
                Forms\Components\DatePicker::make('birth_date')
                    ->label('Fecha de Nacimiento'),
                Forms\Components\KeyValue::make('allergies')
                    ->keyLabel('Alergia')
                    ->valueLabel('Gravedad')
                    ->reorderable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rut')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('health_progress')
                    ->label('Progreso de Salud')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn (Patient $record): string => Pages\HealthProgress::getUrl(['record' => $record]))
                    ->color('info'),
                Action::make('portal_link')
                    ->label('Portal')
                    ->icon('heroicon-o-link')
                    ->url(function (Patient $record) {
                        try {
                            return URL::signedRoute('portal.dashboard', ['tenant' => tenant('id') ?: request()->segment(1), 'patient' => $record]);
                        } catch (\Exception $e) {
                            return '#';
                        }
                    })
                    ->openUrlInNewTab(),
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
            RelationManagers\OdontogramsRelationManager::class,
            RelationManagers\ClinicalHistoryRelationManager::class,
            RelationManagers\PrescriptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
            'odontograms.view' => Pages\ViewOdontogram::route('/{patient}/odontograms/{odontogram}'),
            'health-progress' => Pages\HealthProgress::route('/{record}/health-progress'),
        ];
    }
}
