<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Odontogram;
use Illuminate\Support\Facades\Gate;

$user = User::where('email', 'house@clinic1.com')->first();
setPermissionsTeamId($user->clinic_id);

echo "User: " . $user->email . "\n";
echo "Team ID: " . getPermissionsTeamId() . "\n";

$check = Gate::forUser($user)->check('create', Odontogram::class);
echo "Gate check ('create', Odontogram::class): " . ($check ? 'ALLOW' : 'DENY') . "\n";

$policy = new \App\Policies\OdontogramPolicy();
echo "Policy create check: " . ($policy->create($user) ? 'ALLOW' : 'DENY') . "\n";
