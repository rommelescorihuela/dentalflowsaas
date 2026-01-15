<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->confirmed()
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create'),
                TextInput::make('password_confirmation')
                    ->password()
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->visible(fn(string $operation, $get): bool => $operation === 'create' || filled($get('password'))),
                \Filament\Forms\Components\Select::make('tenant_id')
                    ->label('Clinic (Tenant)')
                    ->relationship('clinic', 'name')
                    ->searchable()
                    ->preload()
                    ->live(), // Important to reload roles when this changes

                \Filament\Forms\Components\Select::make('roles')
                    ->label('Roles (Tenant Scoped)')
                    ->options(function ($get) {
                        $tenantId = $get('tenant_id');
                        if (!$tenantId) {
                            return [];
                        }
                        return \App\Models\Role::withoutGlobalScopes()
                            ->where('roles.clinic_id', $tenantId)
                            ->pluck('name', 'id');
                    })
                    ->multiple()
                    ->saveRelationshipsUsing(function (\Illuminate\Database\Eloquent\Model $record, $state) {
                        $tenantId = $record->tenant_id;

                        // Delete existing role assignments for this tenant
                        \Illuminate\Support\Facades\DB::table('model_has_roles')
                            ->where('model_id', $record->id)
                            ->where('model_type', get_class($record))
                            ->where('clinic_id', $tenantId)
                            ->delete();

                        // Insert new role assignments with correct clinic_id
                        if (!empty($state)) {
                            $inserts = [];
                            foreach ($state as $roleId) {
                                $inserts[] = [
                                    'role_id' => $roleId,
                                    'model_type' => get_class($record),
                                    'model_id' => $record->id,
                                    'clinic_id' => $tenantId,
                                ];
                            }
                            \Illuminate\Support\Facades\DB::table('model_has_roles')->insert($inserts);
                        }

                        // Clear permission cache
                        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
                    })
                    ->loadStateFromRelationshipsUsing(function ($component, $record) {
                        if (!$record || !$record->tenant_id) {
                            return $component->state([]);
                        }

                        // Direct query to bypass Spatie's Team ID scope
                        $roleIds = \Illuminate\Support\Facades\DB::table('model_has_roles')
                            ->where('model_id', $record->id)
                            ->where('model_type', get_class($record))
                            ->where('clinic_id', $record->tenant_id)
                            ->pluck('role_id')
                            ->toArray();

                        return $component->state($roleIds);
                    }),
            ]);
    }
}
