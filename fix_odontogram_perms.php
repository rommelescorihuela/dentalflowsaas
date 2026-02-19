<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Database: " . DB::connection()->getDatabaseName() . " ===" . PHP_EOL;
echo "Total permissions: " . DB::table('permissions')->count() . PHP_EOL;

// Check if Odontogram permissions exist
$existing = DB::table('permissions')->where('name', 'like', '%Odontogram%')->pluck('name')->toArray();
echo "Existing Odontogram perms: " . json_encode($existing) . PHP_EOL;

// Create missing permissions
$perms = [
    'ViewAny:Odontogram', 'View:Odontogram', 'Create:Odontogram', 'Update:Odontogram', 'Delete:Odontogram', 'Restore:Odontogram', 'ForceDelete:Odontogram',
    'ViewAny:ClinicalRecord', 'View:ClinicalRecord', 'Create:ClinicalRecord', 'Update:ClinicalRecord', 'Delete:ClinicalRecord', 'Restore:ClinicalRecord', 'ForceDelete:ClinicalRecord',
];

foreach ($perms as $pName) {
    $p = DB::table('permissions')->where('name', $pName)->first();
    if (!$p) {
        $id = DB::table('permissions')->insertGetId([
            'name' => $pName,
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "Created: $pName (ID: $id)" . PHP_EOL;
    }
    else {
        echo "Exists: $pName (ID: $p->id)" . PHP_EOL;
        $id = $p->id;
    }

    // Assign to role 3 (admin for clinic1)
    if (!DB::table('role_has_permissions')->where('role_id', 3)->where('permission_id', $id)->exists()) {
        DB::table('role_has_permissions')->insert([
            'role_id' => 3,
            'permission_id' => $id,
        ]);
        echo "  -> Assigned to role 3" . PHP_EOL;
    }
    else {
        echo "  -> Already assigned to role 3" . PHP_EOL;
    }
}

// Clear Spatie cache
app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
echo PHP_EOL . "=== Cache cleared ===" . PHP_EOL;

// Verify
$user = \App\Models\User::find(2);
app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId('clinic1');
echo "User 2 can ViewAny:Odontogram? " . ($user->can('ViewAny:Odontogram') ? 'YES' : 'NO') . PHP_EOL;
echo "Done!" . PHP_EOL;