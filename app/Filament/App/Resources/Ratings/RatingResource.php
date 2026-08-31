<?php

namespace App\Filament\App\Resources\Ratings;

use App\Models\Rating;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Calificaciones';

    protected static ?string $modelLabel = 'Calificación';

    protected static ?string $pluralModelLabel = 'Calificaciones';

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
                Forms\Components\Toggle::make('featured')
                    ->label('Destacado')
                    ->default(false),
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
                Tables\Columns\TextColumn::make('rating')
                    ->label('Calificación')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Comentario')
                    ->limit(50),
                Tables\Columns\IconColumn::make('featured')
                    ->label('Destacado')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('featured')
                    ->label('Destacados')
                    ->query(fn (Builder $query): Builder => $query->where('featured', true)),
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Calificación mínima')
                    ->options([
                        4 => '4+ estrellas',
                        3 => '3+ estrellas',
                        2 => '2+ estrellas',
                    ])
                    ->modifyQueryUsing(fn (Builder $query, array $data): Builder => $data['value'] ? $query->where('rating', '>=', $data['value']) : $query),
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
            'index' => Pages\ListRatings::route('/'),
            'create' => Pages\CreateRating::route('/create'),
            'edit' => Pages\EditRating::route('/{record}/edit'),
        ];
    }
}
