<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('email', 'house@clinic1.com')->first();
echo "User: " . $user->email . "\n";
echo "Clinic: " . $user->clinic_id . "\n";

setPermissionsTeamId($user->clinic_id);
echo "Permissions Team ID set to: " . $user->clinic_id . "\n";

// Refresh roles/permissions
$user->unsetRelation('roles');
$user->unsetRelation('permissions');

echo "Can Create:Odontogram? " . ($user->can('Create:Odontogram') ? 'YES' : 'NO') . "\n";
echo "User Roles:\n";
print_r($user->getRoleNames()->toArray());

echo "\nPermissions according to Spatie for this team:\n";
print_r($user->getAllPermissions()->pluck('name')->toArray());
