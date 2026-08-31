<?php

namespace App\Filament\App\Resources\Patients\RelationManagers;

use App\Models\ToothImage;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ToothImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'toothImages';

    protected static ?string $title = 'Imágenes Dentales';

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
                Select::make('image_type')
                    ->label('Tipo de Imagen')
                    ->options([
                        'clinical' => 'Clínica',
                        'radiograph' => 'Radiografía',
                        'before' => 'Antes',
                        'after' => 'Después',
                    ])
                    ->default('clinical')
                    ->required(),
                FileUpload::make('file_path')
                    ->label('Imagen')
                    ->image()
                    ->directory('tooth-images')
                    ->required(),
                TextInput::make('file_name')
                    ->label('Nombre del Archivo')
                    ->required(),
                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3),
                DatePicker::make('image_date')
                    ->label('Fecha de Imagen')
                    ->required()
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_name')
            ->columns([
                ImageColumn::make('file_path')
                    ->label('Imagen')
                    ->circular(),
                TextColumn::make('tooth_number')
                    ->label('Diente')
                    ->sortable(),
                TextColumn::make('image_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'clinical' => 'blue',
                        'radiograph' => 'purple',
                        'before' => 'gray',
                        'after' => 'green',
                        default => 'gray',
                    }),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(30),
                TextColumn::make('image_date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('uploader.name')
                    ->label('Subido por')
                    ->toggleable(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->label('Nueva Imagen')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['clinic_id'] = auth()->user()->clinic_id;
                        $data['patient_id'] = $this->getOwnerRecord()->patient_id;
                        $data['odontogram_id'] = $this->getOwnerRecord()->id;
                        $data['uploaded_by'] = auth()->id();

                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil'),
                DeleteAction::make()
                    ->icon('heroicon-o-trash'),
                Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->modalContent(fn (ToothImage $record) => view('filament.components.tooth-image-modal', ['image' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
            ]);
    }
}
