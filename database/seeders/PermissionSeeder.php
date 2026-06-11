<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions for each model
        // Automatically discover models from Filament Resources
        $modelNames = [];

        // Scan both Admin and App panel resources
        $resourcePaths = [
            app_path('Filament/Resources'),
            app_path('Filament/App/Resources'),
        ];

        foreach ($resourcePaths as $resourcesPath) {
            if (!file_exists($resourcesPath)) {
                continue;
            }

            $files = \Illuminate\Support\Facades\File::allFiles($resourcesPath);

            foreach ($files as $file) {
                // Determine the namespace based on the path
                if (str_contains($resourcesPath, 'Filament/App/Resources')) {
                    $namespace = 'App\\Filament\\App\\Resources\\';
                } else {
                    $namespace = 'App\\Filament\\Resources\\';
                }

                $class = $namespace . str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());

                // Check if it's a valid Resource class
                if (class_exists($class) && is_subclass_of($class, \Filament\Resources\Resource::class)) {
                    $model = $class::getModel();
                    if ($model) {
                        $modelName = class_basename($model);
                        // Avoid duplicates and internal models if needed
                        if (!in_array($modelName, $modelNames)) {
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

        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
        $assistantRole = Role::firstOrCreate(['name' => 'assistant']);

        // Assign permissions to roles
        $superAdminRole->syncPermissions($permissions); // Super admin gets all permissions (globally)
        $adminRole->syncPermissions($permissions);      // Admin gets all permissions per clinic

        // Doctor: full CRUD on clinical resources
        $doctorResources = ['Patient', 'Appointment', 'Odontogram', 'ClinicalRecord', 'Budget', 'Payment'];
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