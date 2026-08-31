<?php

namespace App\Filament\App\Resources\Patients;

use App\Mail\PortalWelcome;
use App\Models\Patient;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
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
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activo',
                        'inactive' => 'Inactivo',
                        default => ucfirst($state),
                    }),
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
                Action::make('send_portal_access')
                    ->label('Enviar acceso')
                    ->icon('heroicon-o-envelope')
                    ->action(function (Patient $record) {
                        try {
                            $portalUrl = URL::signedRoute('portal.dashboard', ['tenant' => tenant('id') ?: request()->segment(1), 'patient' => $record]);
                            Mail::to($record->email)->send(new PortalWelcome($record, $portalUrl));
                            Notification::make()
                                ->title('Email enviado')
                                ->body('Se envió el enlace del portal a '.$record->email)
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('No se pudo enviar el email.')
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Patient $record) => $record->status === 'active'),
                Action::make('deactivate')
                    ->label('Finalizar tratamiento')
                    ->icon('heroicon-o-check-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Finalizar tratamiento')
                    ->modalDescription('El paciente pasará a estado inactivo y dejará de ver presupuestos y pagos activos.')
                    ->action(function (Patient $record) {
                        $record->deactivate();
                        Notification::make()
                            ->title('Tratamiento finalizado')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Patient $record) => $record->status === 'active'),
                Action::make('activate')
                    ->label('Reactivar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->action(function (Patient $record) {
                        $record->activate();
                        Notification::make()
                            ->title('Paciente reactivado')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Patient $record) => $record->status !== 'active'),
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
