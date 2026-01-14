<?php

namespace App\Filament\App\Resources\Patients\RelationManagers;

use App\Filament\App\Resources\Patients\PatientResource;
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
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OdontogramsRelationManager extends RelationManager
{
    protected static string $relationship = 'odontograms';

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
                CreateAction::make()
                    ->before(function (CreateAction $action) {
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

                            $action->halt();
                        }
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['tenant_id'] = auth()->user()->tenant_id;
                        return $data;
                    }),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make()
                    ->url(fn($record) => PatientResource::getUrl('odontograms.view', [
                        'patient' => $record->patient_id,
                        'odontogram' => $record->id,
                    ])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
