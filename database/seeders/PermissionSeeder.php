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
        $models = ['Patient', 'Appointment', 'Budget', 'Odontogram', 'ClinicalRecord'];

        $permissions = [];
        foreach ($models as $model) {
            $permissions[] = Permission::firstOrCreate(['name' => "ViewAny:{$model}"]);
            $permissions[] = Permission::firstOrCreate(['name' => "View:{$model}"]);
            $permissions[] = Permission::firstOrCreate(['name' => "Create:{$model}"]);
            $permissions[] = Permission::firstOrCreate(['name' => "Update:{$model}"]);
            $permissions[] = Permission::firstOrCreate(['name' => "Delete:{$model}"]);
            $permissions[] = Permission::firstOrCreate(['name' => "Restore:{$model}"]);
            $permissions[] = Permission::firstOrCreate(['name' => "ForceDelete:{$model}"]);
        }

        // Find the user and assign permissions directly with their team
        $user = \App\Models\User::where('email', 'alpha@admin')->first();
        if ($user && $user->tenant_id) {
            // Assign permissions with the user's tenant as team
            foreach ($permissions as $permission) {
                $user->givePermissionTo($permission, $user->tenant_id);
            }
        }
    }
}