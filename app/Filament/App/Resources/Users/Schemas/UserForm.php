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
                    ->label('Email address')
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
                    ->relationship('roles', 'name', function ($query) {
                        $clinicId = tenant('id') ?? auth()->user()?->clinic_id;
                        if ($clinicId) {
                            $query->where(function ($q) use ($clinicId) {
                                $q->where('roles.clinic_id', $clinicId)
                                    ->orWhereNull('roles.clinic_id');
                            });
                        }
                        return $query;
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
                    ->preload()
                    ->searchable(),
            ]);
    }
}