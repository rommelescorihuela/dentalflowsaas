<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Role;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('email')
                    ->label('Correo Electronico')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                DateTimePicker::make('email_verified_at')
                    ->label('Verificado'),
                TextInput::make('password')
                    ->label('Contrasena')
                    ->password()
                    ->confirmed()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                TextInput::make('password_confirmation')
                    ->label('Confirmar Contrasena')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->visible(fn (string $operation, $get): bool => $operation === 'create' || filled($get('password'))),
                Select::make('clinic_id')
                    ->label('Clínica')
                    ->relationship('clinic', 'name')
                    ->searchable()
                    ->preload()
                    ->live(), // Important to reload roles when this changes

                Select::make('roles')
                    ->label('Roles (Por Clínica)')
                    ->options(function ($get) {
                        $tenantId = $get('clinic_id');
                        $query = Role::withoutGlobalScopes();
                        if ($tenantId) {
                            $query->where(function ($q) use ($tenantId) {
                                $q->where('clinic_id', $tenantId)
                                    ->orWhereNull('clinic_id');
                            });
                        }

                        // No tenant selected: show all roles (global + tenant scoped)
                        return $query->pluck('name', 'id');
                    })
                    ->multiple()
                    ->saveRelationshipsUsing(function (Model $record, $state) {
                        $tenantId = $record->clinic_id;

                        // If a tenant is selected, set Spatie team context so role insertion uses the correct clinic_id
                        if (! is_null($tenantId)) {
                            setPermissionsTeamId($tenantId);
                        }

                        // Delete existing role assignments
                        if (is_null($tenantId)) {
                            // Global user: remove only global role assignments (clinic_id = null)
                            DB::table('model_has_roles')
                                ->where('model_id', $record->id)
                                ->where('model_type', get_class($record))
                                ->whereNull('clinic_id')
                                ->delete();
                        } else {
                            // Tenant‑scoped user: remove roles for this tenant **and** any global roles
                            // First, delete tenant‑specific assignments
                            DB::table('model_has_roles')
                                ->where('model_id', $record->id)
                                ->where('model_type', get_class($record))
                                ->where('clinic_id', $tenantId)
                                ->delete();
                            // Then, delete any global role assignments (clinic_id = null) to avoid lingering admin rights
                            DB::table('model_has_roles')
                                ->where('model_id', $record->id)
                                ->where('model_type', get_class($record))
                                ->whereNull('clinic_id')
                                ->delete();
                        }

                        // Insert new role assignments with correct clinic_id
                        if (! empty($state)) {
                            $inserts = [];
                            foreach ($state as $roleId) {
                                $inserts[] = [
                                    'role_id' => $roleId,
                                    'model_type' => get_class($record),
                                    'model_id' => $record->id,
                                    'clinic_id' => $tenantId,
                                ];
                            }
                            DB::table('model_has_roles')->insert($inserts);
                        }

                        // Clear permission cache
                        app(PermissionRegistrar::class)->forgetCachedPermissions();
                    })
                    ->loadStateFromRelationshipsUsing(function ($component, $record) {
                        if (! $record || ! $record->clinic_id) {
                            return $component->state([]);
                        }

                        // Direct query to bypass Spatie's Team ID scope
                        $roleIds = DB::table('model_has_roles')
                            ->where('model_id', $record->id)
                            ->where('model_type', get_class($record))
                            ->where('clinic_id', $record->clinic_id)
                            ->pluck('role_id')
                            ->toArray();

                        return $component->state($roleIds);
                    }),
            ]);
    }
}
