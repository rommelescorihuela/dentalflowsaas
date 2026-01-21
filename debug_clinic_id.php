<?php

use App\Models\User;
use App\Models\Role;

// Set team context
setPermissionsTeamId('clinic1');

$user = User::where('email', 'house@clinic1.com')->first();

echo "=== Clinic ID Standardization Debug ===\n\n";

echo "User: {$user->name} ({$user->email})\n";
echo "Clinic ID: " . ($user->clinic_id ?? 'NULL') . "\n";
echo "Current Team ID: " . getPermissionsTeamId() . "\n\n";

echo "User Permission Checks (Odontogram):\n";
$checks = [
    'ViewAny:Odontogram',
    'Create:Odontogram',
];

foreach ($checks as $permission) {
    $can = $user->can($permission) ? '✓' : '✗';
    echo "  {$can} {$permission}\n";
}

echo "\nChecking other models (Patient):\n";
$patient = \App\Models\Patient::first();
echo "Patient Name: " . ($patient->name ?? 'N/A') . "\n";
echo "Patient Clinic ID: " . ($patient->clinic_id ?? 'NULL') . "\n";

echo "\n=== End Debug ===\n";
