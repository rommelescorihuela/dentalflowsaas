<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$user = User::where('email', 'house@clinic1.com')->first();
if (!$user) {
    echo "User not found\n";
    exit;
}

echo "User ID: " . $user->id . "\n";
echo "User Clinic ID: " . $user->clinic_id . "\n";

echo "\nColumns in model_has_roles:\n";
print_r(Schema::getColumnListing('model_has_roles'));

echo "\nRows in model_has_roles for this user:\n";
$rows = DB::table('model_has_roles')->where('model_id', $user->id)->get();
print_r($rows->toArray());

echo "\nRoles in database:\n";
$roles = DB::table('roles')->get();
print_r($roles->toArray());
