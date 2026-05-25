<?php

namespace App\Filament\App\Resources\Patients;

use App\Filament\App\Resources\Patients\Pages;
use App\Models\Patient;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pacientes';

    protected static ?string $navigationGroup = 'Gestión Clínica';

    public static function getPluralModelLabel(): string
    {
        return 'Pacientes';
    }

    public static function getModelLabel(): string
    {
        return 'Paciente';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('rut')
                    ->label('RUT / DNI')
                    ->maxLength(20),
                Forms\Components\DatePicker::make('birth_date'),
                Forms\Components\Select::make('doctor_id')
                    ->relationship('doctor', 'name')
                    ->label('Doctor Asignado')
                    ->searchable(),
                Forms\Components\KeyValue::make('allergies')
                    ->keyLabel('Alergia')
                    ->valueLabel('Severidad')
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
                Tables\Filters\SelectFilter::make('doctor')
                    ->relationship('doctor', 'name')
                    ->label('Doctor Asignado')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'] ?? null, fn($q) => $q->whereDate('created_at', '>=', $data['created_from']))
                            ->when($data['created_until'] ?? null, fn($q) => $q->whereDate('created_at', '<=', $data['created_until']));
                    }),
            ])
            ->actions([
                EditAction::make(),
                Action::make('health_progress')
                    ->label('Historial Salud')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn(Patient $record): string => Pages\HealthProgress::getUrl(['record' => $record]))
                    ->color('info'),
                Action::make('portal_link')
                    ->label('Portal Paciente')
                    ->icon('heroicon-o-link')
                    ->url(function (Patient $record) {
                        try {
                            return \Illuminate\Support\Facades\URL::signedRoute('portal.dashboard', ['tenant' => tenant('id') ?: request()->segment(1), 'patient' => $record]);
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