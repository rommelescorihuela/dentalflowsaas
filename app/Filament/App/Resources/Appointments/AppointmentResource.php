<?php

namespace App\Filament\App\Resources\Appointments;

use App\Filament\App\Resources\Appointments\Pages;
use App\Models\Appointment;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Citas';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión Clínica';

    public static function getPluralModelLabel(): string
    {
        return 'Citas';
    }

    public static function getModelLabel(): string
    {
        return 'Cita';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            Forms\Components\Select::make('patient_id')
            ->relationship('patient', 'name')
            ->required()
            ->searchable(),
            Forms\Components\Select::make('procedure_price_id')
            ->relationship('procedurePrice', 'procedure_name')
            ->searchable()
            ->preload()
            ->label('Procedimiento')
            ->required(),
            Forms\Components\DateTimePicker::make('start_time')
            ->required(),
            Forms\Components\DateTimePicker::make('end_time')
            ->required(),
            Forms\Components\Select::make('status')
            ->options([
                'scheduled' => 'Programada',
                'confirmed' => 'Confirmada',
                'completed' => 'Completada',
                'cancelled' => 'Cancelada',
            ])
            ->required(),
            Forms\Components\Select::make('type')
            ->options([
                'control' => 'Control',
                'urgent' => 'Urgente',
                'cleaning' => 'Limpieza',
                'surgery' => 'Cirugía',
            ])
            ->required(),
            Forms\Components\Textarea::make('notes')
            ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('patient.name')
            ->searchable()
            ->sortable(),
            Tables\Columns\TextColumn::make('start_time')
            ->dateTime()
            ->sortable(),
            Tables\Columns\TextColumn::make('status')
            ->badge()
            ->color(fn(string $state): string => match ($state) {
            'scheduled' => 'gray',
            'confirmed' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'gray',
        }),
            Tables\Columns\TextColumn::make('type'),
        ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Programada',
                        'confirmed' => 'Confirmada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ])
                    ->label('Estado'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'control' => 'Control',
                        'urgent' => 'Urgente',
                        'cleaning' => 'Limpieza',
                        'surgery' => 'Cirugía',
                    ])
                    ->label('Tipo'),
                Tables\Filters\SelectFilter::make('patient.doctor')
                    ->relationship('patient.doctor', 'name')
                    ->label('Doctor del Paciente')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('start_time')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['date_from'] ?? null, fn($q) => $q->whereDate('start_time', '>=', $data['date_from']))
                            ->when($data['date_until'] ?? null, fn($q) => $q->whereDate('start_time', '<=', $data['date_until']));
                    }),
            ])
            ->actions([
            \Filament\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->check() && auth()->user()->hasRole('doctor')) {
            // Doctors only see their own appointments
            $query->where('user_id', auth()->id());
        }
        // Admin and assistant roles see all appointments

        return $query;
    }
}