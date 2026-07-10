<?php

namespace App\Filament\App\Resources\Roles\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
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
                            ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule) {
                                return $rule->where('clinic_id', Filament::getTenant()?->id);
                            })
                            ->helperText('ej: doctor, recepcionista, asistente'),

                        TextInput::make('clinic_id')
                            ->label('Clínica')
                            ->default(fn () => Filament::getTenant()?->id)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Asignado automaticamente a la clinica actual'),

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
                            ->helperText('Seleccionar los permisos para este rol'),
                    ]),
            ]);
    }
}
