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
                        return $query->where('clinic_id', \Filament\Facades\Filament::getTenant()->id);
                    })
                    ->saveRelationshipsUsing(function ($record, $state) {
                        $tenantId = \Filament\Facades\Filament::getTenant()->id;
                        $record->roles()->wherePivot('clinic_id', $tenantId)->detach();
                        foreach ($state as $roleId) {
                            $record->roles()->attach($roleId, ['clinic_id' => $tenantId]);
                        }
                    })
                    ->preload()
                    ->searchable(),
            ]);
    }
}