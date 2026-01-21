<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Roles in DB:\n";
$roles = DB::table('roles')->get();
foreach ($roles as $role) {
    echo "ID: " . $role->id . " Name: " . $role->name . " Clinic: " . ($role->clinic_id ?? 'GLOBAL') . "\n";

    $hasCreate = DB::table('role_has_permissions')
        ->where('role_id', $role->id)
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('permissions')
                ->whereColumn('permissions.id', 'role_has_permissions.permission_id')
                ->where('name', 'Create:Odontogram');
        })
        ->exists();
    echo "  Has Create:Odontogram? " . ($hasCreate ? 'YES' : 'NO') . "\n";
}
