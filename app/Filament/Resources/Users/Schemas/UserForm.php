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
                    ->label('Nombre')
                    ->required(),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                DateTimePicker::make('email_verified_at')
                    ->label('Fecha de Verificación'),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->confirmed()
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create'),
                TextInput::make('password_confirmation')
                    ->label('Confirmar Contraseña')
                    ->password()
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->visible(fn(string $operation, $get): bool => $operation === 'create' || filled($get('password'))),
                \Filament\Forms\Components\Select::make('clinic_id')
                    ->label('Clínica (Tenant)')
                    ->relationship('clinic', 'name')
                    ->searchable()
                    ->preload()
                    ->live(),

                \Filament\Forms\Components\Select::make('roles')
                    ->label('Roles (Ámbito Tenant)')
                    ->options(function ($get) {
                        $tenantId = $get('clinic_id');
                        $query = \App\Models\Role::withoutGlobalScopes();
                        if ($tenantId) {
                            $query->where(function ($q) use ($tenantId) {
                                $q->where('clinic_id', $tenantId)
                                    ->orWhereNull('clinic_id');
                            });
                        }
                        return $query->pluck('name', 'id');
                    })
                    ->multiple()
                    ->saveRelationshipsUsing(function (\Illuminate\Database\Eloquent\Model $record, $state) {
                        $tenantId = $record->clinic_id;

                        if (!is_null($tenantId)) {
                            setPermissionsTeamId($tenantId);
                        }

                        if (is_null($tenantId)) {
                            \Illuminate\Support\Facades\DB::table('model_has_roles')
                                ->where('model_id', $record->id)
                                ->where('model_type', get_class($record))
                                ->whereNull('clinic_id')
                                ->delete();
                        } else {
                            \Illuminate\Support\Facades\DB::table('model_has_roles')
                                ->where('model_id', $record->id)
                                ->where('model_type', get_class($record))
                                ->where('clinic_id', $tenantId)
                                ->delete();
                            \Illuminate\Support\Facades\DB::table('model_has_roles')
                                ->where('model_id', $record->id)
                                ->where('model_type', get_class($record))
                                ->whereNull('clinic_id')
                                ->delete();
                        }

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

                        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
                    })
                    ->loadStateFromRelationshipsUsing(function ($component, $record) {
                        if (!$record || !$record->clinic_id) {
                            return $component->state([]);
                        }

                        $roleIds = \Illuminate\Support\Facades\DB::table('model_has_roles')
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
