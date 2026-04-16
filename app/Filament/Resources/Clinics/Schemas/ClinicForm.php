<?php

namespace App\Filament\Resources\Clinics\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
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
                Section::make('Configuración de Dominio')
                    ->description('Instrucciones para apuntar un dominio personalizado a esta clínica.')
                    ->collapsible()
                    ->schema([
                        Placeholder::make('dns_instructions')
                            ->label('Pasos a seguir:')
                            ->content('1. Pídale al cliente que cree un registro "A" en su DNS apuntando a la IP de este servidor.
2. Una vez propagado, añada el dominio en la pestaña "Dominios" de abajo.
3. El sistema reconocerá automáticamente la clínica al acceder por ese dominio.'),
                    ]),
            ]);
    }
}
