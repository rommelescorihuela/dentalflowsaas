<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "role_has_permissions for role 7 (doctor):\n";
$rows = DB::table('role_has_permissions')->where('role_id', 7)->get();
print_r($rows->toArray());

echo "\nPermissions linked to these rows:\n";
foreach ($rows as $row) {
    $perm = DB::table('permissions')->where('id', $row->permission_id)->first();
    echo "ID: " . $row->permission_id . " Name: " . ($perm->name ?? 'N/A') . " Clinic ID in pivot: " . ($row->clinic_id ?? 'NULL') . "\n";
}
