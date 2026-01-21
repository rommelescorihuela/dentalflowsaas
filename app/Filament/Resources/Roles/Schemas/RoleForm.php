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
                Section::make('Role Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Role Name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('e.g., super_admin, global_manager'),

                        Select::make('clinic_id')
                            ->label('Clinic (Leave empty for global role)')
                            ->relationship('clinic', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Leave empty to create a global role'),

                        TextInput::make('guard_name')
                            ->label('Guard')
                            ->default('web')
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                Section::make('Permissions')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('Assign Permissions')
                            ->relationship('permissions', 'name')
                            ->options(Permission::all()->pluck('name', 'id'))
                            ->columns(3)
                            ->searchable()
                            ->bulkToggleable()
                            ->helperText('Select the permissions for this role'),
                    ]),
            ]);
    }
}
