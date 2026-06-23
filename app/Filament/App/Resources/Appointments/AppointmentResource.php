<?php

namespace App\Filament\App\Resources\Appointments;

use App\Helpers\ClinicHelper;
use App\Models\Appointment;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Citas';

    protected static ?string $modelLabel = 'Cita';

    protected static ?string $pluralModelLabel = 'Citas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('patient_id')
                    ->label('Paciente')
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
                    ->label('Inicio')
                    ->required()
                    ->after('now')
                    ->afterOrEqual(function () {
                        $scheduleStart = ClinicHelper::getScheduleStart();

                        return $scheduleStart ? now()->setTimeFromTimeString($scheduleStart) : now();
                    })
                    ->beforeOrEqual(function ($get) {
                        return $get('end_time') ?? now()->addHours(8);
                    })
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                $scheduleStart = ClinicHelper::getScheduleStart();
                                $scheduleEnd = ClinicHelper::getScheduleEnd();

                                if ($scheduleStart && $scheduleEnd) {
                                    $time = Carbon::parse($value);
                                    $startTime = Carbon::parse($scheduleStart);
                                    $endTime = Carbon::parse($scheduleEnd);

                                    if ($time->lt($startTime) || $time->gt($endTime)) {
                                        $fail("La hora de inicio debe estar dentro del horario de la clínica ({$scheduleStart} - {$scheduleEnd}).");
                                    }
                                }
                            };
                        },
                    ]),
                Forms\Components\DateTimePicker::make('end_time')
                    ->label('Fin')
                    ->required()
                    ->after('start_time')
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                $scheduleStart = ClinicHelper::getScheduleStart();
                                $scheduleEnd = ClinicHelper::getScheduleEnd();

                                if ($scheduleStart && $scheduleEnd) {
                                    $time = Carbon::parse($value);
                                    $startTime = Carbon::parse($scheduleStart);
                                    $endTime = Carbon::parse($scheduleEnd);

                                    if ($time->lt($startTime) || $time->gt($endTime)) {
                                        $fail("La hora de fin debe estar dentro del horario de la clínica ({$scheduleStart} - {$scheduleEnd}).");
                                    }
                                }
                            };
                        },
                    ]),
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'scheduled' => 'Programada',
                        'confirmed' => 'Confirmada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ])
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'control' => 'Control',
                        'urgent' => 'Urgencia',
                        'cleaning' => 'Limpieza',
                        'surgery' => 'Cirugia',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Notas')
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
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'gray',
                        'confirmed' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('type'),
            ])
            ->filters([
                            //
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
