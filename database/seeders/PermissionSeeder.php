<?php

namespace Database\Seeders;

use Filament\Resources\Resource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset team context to null for central permissions/roles
        setPermissionsTeamId(null);

        // Create permissions for each model
        // Automatically discover models from Filament Resources
        $modelNames = [];

        // Scan both Admin and App panel resources
        $resourcePaths = [
            app_path('Filament/Resources'),
            app_path('Filament/App/Resources'),
        ];

        foreach ($resourcePaths as $resourcesPath) {
            if (! file_exists($resourcesPath)) {
                continue;
            }

            $files = File::allFiles($resourcesPath);

            foreach ($files as $file) {
                // Determine the namespace based on the path
                if (str_contains($resourcesPath, 'Filament/App/Resources')) {
                    $namespace = 'App\\Filament\\App\\Resources\\';
                } else {
                    $namespace = 'App\\Filament\\Resources\\';
                }

                $class = $namespace.str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());

                // Check if it's a valid Resource class
                if (class_exists($class) && is_subclass_of($class, \Filament\Resources\Resource::class)) {
                    $model = $class::getModel();
                    if ($model) {
                        $modelName = class_basename($model);
                        // Avoid duplicates and internal models if needed
                        if (! in_array($modelName, $modelNames)) {
                            $modelNames[] = $modelName;
                        }
                    }
                }
            }
        }

        // Add any extra models that might not have a Resource but need permissions
        $extraModels = ['Odontogram'];
        $modelNames = array_merge($modelNames, $extraModels);

        $permissions = [];
        foreach ($modelNames as $model) {
            $permissions[] = Permission::firstOrCreate(['name' => "ViewAny:{$model}"]);
            $permissions[] = Permission::firstOrCreate(['name' => "View:{$model}"]);
            $permissions[] = Permission::firstOrCreate(['name' => "Create:{$model}"]);
            $permissions[] = Permission::firstOrCreate(['name' => "Update:{$model}"]);
            $permissions[] = Permission::firstOrCreate(['name' => "Delete:{$model}"]);
            $permissions[] = Permission::firstOrCreate(['name' => "Restore:{$model}"]);
            $permissions[] = Permission::firstOrCreate(['name' => "ForceDelete:{$model}"]);
        }

        // Create Roles (always global: clinic_id = null)
        // Roles are global definitions; tenant scoping happens at the user-role pivot level
        foreach (['super-admin', 'admin', 'doctor', 'assistant'] as $roleName) {
            $existing = Role::where('name', $roleName)->get();

            if ($existing->count() > 1) {
                // Clean up duplicates: keep one, delete rest, force global
                $keep = $existing->whereNull('clinic_id')->first() ?? $existing->first();
                Role::where('name', $roleName)->where('id', '!=', $keep->id)->delete();
                $keep->update(['clinic_id' => null, 'guard_name' => 'web']);
            } elseif ($existing->count() === 1) {
                $existing->first()->update(['clinic_id' => null, 'guard_name' => 'web']);
            } else {
                Role::create(['name' => $roleName, 'clinic_id' => null, 'guard_name' => 'web']);
            }
        }

        $superAdminRole = Role::where('name', 'super-admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $doctorRole = Role::where('name', 'doctor')->first();
        $assistantRole = Role::where('name', 'assistant')->first();

        // Assign permissions to roles
        $superAdminRole->syncPermissions($permissions); // Super admin gets all permissions (globally)
        $adminRole->syncPermissions($permissions);      // Admin gets all permissions per clinic

        // Doctor: full CRUD on clinical resources
        $doctorResources = ['Patient', 'Appointment', 'Odontogram', 'ClinicalRecord', 'PatientMedicalHistory', 'Prescription', 'Budget', 'Payment'];
        $doctorPermissions = collect($permissions)->filter(function ($p) use ($doctorResources) {
            $parts = explode(':', $p->name);

            return in_array($parts[0], ['ViewAny', 'View', 'Create', 'Update', 'Delete'])
                && in_array($parts[1], $doctorResources);
        });
        $doctorRole->syncPermissions($doctorPermissions);

        // Assistant: limited access (no delete, limited resources)
        $assistantResources = ['Patient', 'Appointment', 'Budget'];
        $assistantPermissions = collect($permissions)->filter(function ($p) use ($assistantResources) {
            $parts = explode(':', $p->name);

            return in_array($parts[0], ['ViewAny', 'View', 'Create', 'Update'])
                && in_array($parts[1], $assistantResources);
        });
        $assistantRole->syncPermissions($assistantPermissions);

        /*
        // Find the user and assign permissions directly with their team
        $user = \App\Models\User::where('email', 'alpha@admin')->first();
        if ($user && $user->clinic_id) {
            // Assign permissions with the user's tenant as team
            foreach ($permissions as $permission) {
                $user->givePermissionTo($permission, $user->clinic_id);
            }
        }
        */
    }
}
