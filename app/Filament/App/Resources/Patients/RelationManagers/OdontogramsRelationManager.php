<?php

namespace App\Filament\App\Resources\Patients\RelationManagers;

use App\Filament\App\Resources\Patients\PatientResource;
use App\Helpers\ClinicHelper;
use App\Models\Budget;
use App\Models\Odontogram;
use App\Services\BudgetGenerator;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OdontogramsRelationManager extends RelationManager
{
    protected static string $relationship = 'odontograms';

    public static function shouldSkipAuthorization(): bool
    {
        return false;
    }

    public function canCreate(): bool
    {
        return auth()->user()->can('create', Odontogram::class);
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ej: Revisión Inicial, Seguimiento 6 Meses'),
                DatePicker::make('date')
                    ->label('Fecha')
                    ->required()
                    ->default(now()),
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'in_progress' => 'En Progreso',
                        'completed' => 'Completado',
                    ])
                    ->default('in_progress')
                    ->required(),
                Textarea::make('notes')
                    ->label('Notas')
                    ->rows(3)
                    ->placeholder('Notas de la sesión...'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in_progress' => 'En Progreso',
                        'completed' => 'Completado',
                        default => ucfirst($state),
                    }),
                TextColumn::make('clinicalRecords_count')
                    ->counts('clinicalRecords')
                    ->label('Registros'),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('create_odontogram')
                    ->label('Nuevo Odontograma')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->form([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Revisión Inicial, Seguimiento 6 Meses'),
                        DatePicker::make('date')
                            ->label('Fecha')
                            ->required()
                            ->default(now()),
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'in_progress' => 'En Progreso',
                                'completed' => 'Completado',
                            ])
                            ->default('in_progress')
                            ->required(),
                        Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3)
                            ->placeholder('Notas de la sesión...'),
                    ])
                    ->action(function (array $data) {
                        // Check if there's an in-progress odontogram
                        $hasInProgress = $this->getOwnerRecord()
                            ->odontograms()
                            ->where('status', 'in_progress')
                            ->exists();

                        if ($hasInProgress) {
                            Notification::make()
                                ->title('No se puede crear un nuevo odontograma')
                                ->body('Por favor completa el odontograma en progreso antes de crear uno nuevo.')
                                ->warning()
                                ->send();

                            return;
                        }

                        $data['clinic_id'] = auth()->user()->clinic_id;
                        $odontogram = $this->getOwnerRecord()->odontograms()->create($data);

                        Notification::make()
                            ->title('Odontograma creado')
                            ->success()
                            ->send();

                        return redirect()->to(PatientResource::getUrl('odontograms.view', [
                            'patient' => $this->getOwnerRecord()->id,
                            'odontogram' => $odontogram->id,
                        ]));
                    }),
            ])
            ->actions([
                Action::make('open')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn ($record) => PatientResource::getUrl('odontograms.view', [
                        'patient' => $record->patient_id,
                        'odontogram' => $record->id,
                    ])),
                Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->url(fn ($record): string => route('odontograms.pdf', $record))
                    ->openUrlInNewTab(),
                Action::make('edit')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->url(fn ($record) => PatientResource::getUrl('odontograms.view', [
                        'patient' => $record->patient_id,
                        'odontogram' => $record->id,
                    ])),
                Action::make('generate_budget')
                    ->label('Generar Presupuesto')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'completed')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Presupuesto desde Odontograma')
                    ->modalDescription('Esto creará un presupuesto en borrador basado en los registros clínicos. Podrás editarlo antes de enviarlo al paciente.')
                    ->modalSubmitActionLabel('Generar')
                    ->action(function (Odontogram $record, BudgetGenerator $generator) {
                        $existing = Budget::where('odontogram_id', $record->id)->first();
                        if ($existing) {
                            Notification::make()
                                ->title('Ya existe un presupuesto')
                                ->body('Ya se generó un presupuesto para este odontograma. Puedes editarlo desde la sección de Presupuestos.')
                                ->warning()
                                ->send();

                            return;
                        }

                        $budget = $generator->generate($record);

                        Notification::make()
                            ->title('Presupuesto generado')
                            ->body('Borrador de presupuesto #'.$budget->id.' creado con total '.ClinicHelper::formatMoneyShort($budget->total).'. Ahora puedes editarlo.')
                            ->success()
                            ->send();
                    }),
                Action::make('delete')
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Odontogram $record) {
                        if ($record->clinic_id !== tenant('id')) {
                            Notification::make()
                                ->title('Acceso denegado')
                                ->body('No tienes permiso para eliminar este odontograma.')
                                ->danger()
                                ->send();

                            return;
                        }
                        $record->delete();
                        Notification::make()
                            ->title('Odontograma eliminado')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
