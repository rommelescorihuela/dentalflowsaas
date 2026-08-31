<?php

namespace App\Filament\App\Resources\ServiceFeedbacks;

use App\Models\ServiceFeedback;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceFeedbackResource extends Resource
{
    protected static ?string $model = ServiceFeedback::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationLabel = 'Feedback de Servicios';

    protected static ?string $modelLabel = 'Feedback';

    protected static ?string $pluralModelLabel = 'Feedbacks';

    protected static string|\UnitEnum|null $navigationGroup = 'Comunicación';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('patient_id')
                    ->label('Paciente')
                    ->relationship('patient', 'name')
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('appointment_id')
                    ->label('Cita')
                    ->relationship('appointment', 'id')
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('user_id')
                    ->label('Doctor')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('category')
                    ->label('Categoría')
                    ->options([
                        'atencion' => 'Atención',
                        'limpieza' => 'Limpieza',
                        'procedimiento' => 'Procedimiento',
                        'instalaciones' => 'Instalaciones',
                        'tiempo_espera' => 'Tiempo de espera',
                        'otro' => 'Otro',
                    ]),
                Forms\Components\Select::make('rating')
                    ->label('Calificación')
                    ->options([
                        1 => '1 - Muy malo',
                        2 => '2 - Malo',
                        3 => '3 - Regular',
                        4 => '4 - Bueno',
                        5 => '5 - Excelente',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('comment')
                    ->label('Comentario')
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'atencion' => 'blue',
                        'limpieza' => 'green',
                        'procedimiento' => 'purple',
                        'instalaciones' => 'orange',
                        'tiempo_espera' => 'yellow',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Calificación')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Comentario')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'atencion' => 'Atención',
                        'limpieza' => 'Limpieza',
                        'procedimiento' => 'Procedimiento',
                        'instalaciones' => 'Instalaciones',
                        'tiempo_espera' => 'Tiempo de espera',
                        'otro' => 'Otro',
                    ]),
            ])
            ->actions([
                EditAction::make(),
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
            'index' => Pages\ListServiceFeedbacks::route('/'),
            'create' => Pages\CreateServiceFeedback::route('/create'),
            'edit' => Pages\EditServiceFeedback::route('/{record}/edit'),
        ];
    }
}
