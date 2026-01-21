<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

app(PermissionRegistrar::class)->forgetCachedPermissions();

$user = User::where('email', 'house@clinic1.com')->first();
echo "User: " . $user->email . "\n";
echo "Clinic: " . $user->clinic_id . "\n";

setPermissionsTeamId($user->clinic_id);
echo "Permissions Team ID set to: " . getPermissionsTeamId() . "\n";

$user->unsetRelation('roles');
$user->unsetRelation('permissions');

echo "can('Create:Odontogram'): " . ($user->can('Create:Odontogram') ? 'YES' : 'NO') . "\n";
echo "hasPermissionTo('Create:Odontogram'): " . ($user->hasPermissionTo('Create:Odontogram') ? 'YES' : 'NO') . "\n";

echo "\nChecking directly via Gate:\n";
echo "Gate::allows('Create:Odontogram'): " . (\Illuminate\Support\Facades\Gate::allows('Create:Odontogram') ? 'YES' : 'NO') . "\n";
// Note: Gate check for the logged in user requires actingAs or similar, but in a script it might use the default.
// Let's force it:
echo "Gate::forUser(\$user)->allows('Create:Odontogram'): " . (\Illuminate\Support\Facades\Gate::forUser($user)->allows('Create:Odontogram') ? 'YES' : 'NO') . "\n";
