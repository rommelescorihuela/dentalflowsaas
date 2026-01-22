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

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

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
                Forms\Components\KeyValue::make('allergies')
                    ->keyLabel('Allergy')
                    ->valueLabel('Severity')
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
                \Filament\Actions\EditAction::make(),
                \Filament\Tables\Actions\Action::make('health_progress')
                    ->label('Health Progress')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn(Patient $record): string => Pages\HealthProgress::getUrl(['record' => $record]))
                    ->color('info'),
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
