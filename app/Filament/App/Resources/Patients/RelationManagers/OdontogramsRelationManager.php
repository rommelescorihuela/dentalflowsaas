<?php

namespace App\Filament\App\Resources\Patients\RelationManagers;

use App\Filament\App\Resources\Patients\PatientResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Odontogram;

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
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Initial Checkup, 6-Month Follow-up'),
                \Filament\Forms\Components\DatePicker::make('date')
                    ->required()
                    ->default(now()),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                    ])
                    ->default('in_progress')
                    ->required(),
                \Filament\Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->placeholder('Session notes...'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('clinicalRecords_count')
                    ->counts('clinicalRecords')
                    ->label('Records'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\Action::make('create_odontogram')
                    ->label('New Odontogram')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->form([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Initial Checkup, 6-Month Follow-up'),
                        \Filament\Forms\Components\DatePicker::make('date')
                            ->required()
                            ->default(now()),
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                            ])
                            ->default('in_progress')
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->placeholder('Session notes...'),
                    ])
                    ->action(function (array $data) {
                        // Check if there's an in-progress odontogram
                        $hasInProgress = $this->getOwnerRecord()
                            ->odontograms()
                            ->where('status', 'in_progress')
                            ->exists();

                        if ($hasInProgress) {
                            \Filament\Notifications\Notification::make()
                                ->title('Cannot create new odontogram')
                                ->body('Please complete the current in-progress odontogram before creating a new one.')
                                ->warning()
                                ->send();

                            return;
                        }

                        $data['clinic_id'] = auth()->user()->clinic_id;
                        $odontogram = $this->getOwnerRecord()->odontograms()->create($data);

                        \Filament\Notifications\Notification::make()
                            ->title('Odontogram created')
                            ->success()
                            ->send();

                        return redirect()->to(PatientResource::getUrl('odontograms.view', [
                            'patient' => $this->getOwnerRecord()->id,
                            'odontogram' => $odontogram->id,
                        ]));
                    }),
            ])
            ->actions([
                \Filament\Actions\Action::make('open')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn($record) => PatientResource::getUrl('odontograms.view', [
                        'patient' => $record->patient_id,
                        'odontogram' => $record->id,
                    ])),
                \Filament\Actions\Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->url(fn($record) => PatientResource::getUrl('odontograms.view', [
                        'patient' => $record->patient_id,
                        'odontogram' => $record->id,
                    ])),
                \Filament\Actions\Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (\App\Models\Odontogram $record) {
                        if ($record->clinic_id !== tenant('id')) {
                            \Filament\Notifications\Notification::make()
                                ->title('Access denied')
                                ->body('No tienes permiso para eliminar este odontograma.')
                                ->danger()
                                ->send();
                            return;
                        }
                        $record->delete();
                        \Filament\Notifications\Notification::make()
                            ->title('Odontogram deleted')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}