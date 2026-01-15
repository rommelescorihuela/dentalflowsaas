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
                    ->relationship('roles', 'name')
                    ->options(function () {
                        $tenantId = \Filament\Facades\Filament::getTenant()->id;
                        return \App\Models\Role::where('clinic_id', $tenantId)->pluck('name', 'id');
                    })
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
