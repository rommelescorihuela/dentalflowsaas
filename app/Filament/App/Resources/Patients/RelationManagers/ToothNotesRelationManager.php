<?php

namespace App\Filament\App\Resources\Patients\RelationManagers;

use App\Models\ToothNote;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ToothNotesRelationManager extends RelationManager
{
    protected static string $relationship = 'toothNotes';

    protected static ?string $title = 'Notas Dentales';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tooth_number')
                    ->label('Número de Diente')
                    ->required()
                    ->numeric()
                    ->minValue(11)
                    ->maxValue(48)
                    ->helperText('Notación FDI: 11-48'),
                Select::make('note_type')
                    ->label('Tipo de Nota')
                    ->options([
                        'observation' => 'Observación',
                        'diagnosis' => 'Diagnóstico',
                        'treatment' => 'Tratamiento',
                        'follow_up' => 'Seguimiento',
                    ])
                    ->default('observation')
                    ->required(),
                Textarea::make('content')
                    ->label('Contenido')
                    ->rows(4)
                    ->required(),
                DatePicker::make('note_date')
                    ->label('Fecha')
                    ->required()
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                TextColumn::make('tooth_number')
                    ->label('Diente')
                    ->sortable(),
                TextColumn::make('note_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'observation' => 'blue',
                        'diagnosis' => 'purple',
                        'treatment' => 'green',
                        'follow_up' => 'orange',
                        default => 'gray',
                    }),
                TextColumn::make('content')
                    ->label('Contenido')
                    ->limit(50),
                TextColumn::make('note_date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('Creado por')
                    ->toggleable(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->label('Nueva Nota')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['clinic_id'] = auth()->user()->clinic_id;
                        $data['patient_id'] = $this->getOwnerRecord()->patient_id;
                        $data['odontogram_id'] = $this->getOwnerRecord()->id;
                        $data['created_by'] = auth()->id();

                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil'),
                DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->defaultSort('note_date', 'desc');
    }
}
