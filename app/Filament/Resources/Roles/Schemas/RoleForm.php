<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Rol')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Rol')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('ej. super_admin, global_manager'),

                        Select::make('clinic_id')
                            ->label('Clínica (Dejar vacío para rol global)')
                            ->relationship('clinic', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Dejar vacío para crear un rol global'),

                        TextInput::make('guard_name')
                            ->label('Guard')
                            ->default('web')
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                Section::make('Permisos')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('Asignar Permisos')
                            ->relationship('permissions', 'name')
                            ->options(Permission::all()->pluck('name', 'id'))
                            ->columns(3)
                            ->searchable()
                            ->bulkToggleable()
                            ->helperText('Seleccione los permisos para este rol'),
                    ]),
            ]);
    }
}
