<?php

namespace App\Filament\App\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->confirmed()
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create'),
                TextInput::make('password_confirmation')
                    ->password()
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->visible(fn(string $operation): bool => $operation === 'create' || filled($operation)),
                \Filament\Forms\Components\Select::make('roles')
                    ->label('Roles')
                    ->multiple()
                    ->options(function () {
                        $clinicId = tenant('id') ?? auth()->user()?->clinic_id;
                        return \App\Models\Role::where('clinic_id', $clinicId)
                            ->pluck('name', 'id');
                    })
                    ->saveRelationshipsUsing(function ($record, $state) {
                        $clinicId = tenant('id') ?? auth()->user()?->clinic_id;
                        if ($clinicId) {
                            $record->roles()->wherePivot('clinic_id', $clinicId)->detach();
                            foreach ($state as $roleId) {
                                $record->roles()->attach($roleId, ['clinic_id' => $clinicId]);
                            }
                        }
                    })
                    ->loadStateFromRelationshipsUsing(function ($component, $record) {
                        if (!$record?->exists) {
                            return $component->state([]);
                        }
                        $clinicId = tenant('id') ?? auth()->user()?->clinic_id;
                        $roleIds = \Illuminate\Support\Facades\DB::table('model_has_roles')
                            ->where('model_id', $record->id)
                            ->where('model_type', get_class($record))
                            ->where('clinic_id', $clinicId)
                            ->pluck('role_id')
                            ->toArray();
                        return $component->state($roleIds);
                    })
                    ->preload()
                    ->searchable(),
            ]);
    }
}