<?php

namespace App\Filament\Resources\Clinics\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ClinicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Información de la Clínica')
                            ->description('Datos básicos de identidad de la sede.')
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('id')
                                    ->label('Identificador Único (Tenant ID)')
                                    ->helperText('No se puede cambiar una vez creado.')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('name')
                                    ->label('Nombre de la Clínica')
                                    ->placeholder('Ej: Clínica Dental Las Mercedes')
                                    ->required(),
                                TextInput::make('plan')
                                    ->label('Plan de Suscripción')
                                    ->placeholder('ej: premium, basic')
                                    ->required()
                                    ->default('free'),
                            ]),

                        Section::make('Ajustes y Configuración')
                            ->description('Valores personalizados guardados en formato Clave/Valor.')
                            ->columnSpan(1)
                            ->schema([
                                KeyValue::make('data')
                                    ->label('Propiedades de Configuración')
                                    ->keyLabel('Atributo')
                                    ->valueLabel('Valor')
                                    ->addActionLabel('Añadir ajuste')
                                    ->helperText('Configura horarios, colores, moneda, etc.'),
                            ]),
                    ]),

                Section::make('Guía: Conexión de Dominio Personalizado')
                    ->description('Sigue estos pasos para que tu clínica use su propio dominio (ej. www.clinica.com)')
                    ->icon('heroicon-o-globe-alt')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Placeholder::make('ip')
                                    ->label('IP del Servidor (Registro A)')
                                    ->content('190.6.60.35')
                                    ->extraAttributes(['class' => 'font-mono text-primary-600 font-bold']),
                                Placeholder::make('step1')
                                    ->label('Paso 1: DNS')
                                    ->content('Crea un registro tipo "A" en el proveedor de dominio apuntando a la IP mostrada a la izquierda.'),
                                Placeholder::make('step2')
                                    ->label('Paso 2: Activar')
                                    ->content('Añade el dominio exacto en la sección de "Dominios" al final de esta página.'),
                            ]),
                    ]),
            ]);
    }
}
