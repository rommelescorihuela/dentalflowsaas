<?php

namespace App\Filament\Resources\Clinics\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClinicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->required(),
                TextInput::make('plan')
                    ->required()
                    ->default('free'),
                TextInput::make('data'),
            ]);
    }
}
