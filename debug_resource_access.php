<?php
// debug_resource_access.php

$tenantId = 'clinic_alpha';
$userEmail = 'alpha@admin.com';

echo "Debugging Resource Access for '$userEmail' in Tenant '$tenantId'...\n";

// 1. Setup User (MUST LOG IN FIRST for Filament::setTenant)
$u = \App\Models\User::where('email', $userEmail)->first();
if (!$u) {
    echo "User not found.\n";
    exit;
}
\Illuminate\Support\Facades\Auth::login($u);
echo "Logged in as: " . $u->email . "\n";

// 2. Setup Tenant
$tenant = \App\Models\Clinic::where('id', $tenantId)->first();
if (!$tenant) {
    echo "Tenant not found.\n";
    exit;
}
\Filament\Facades\Filament::setTenant($tenant);
setPermissionsTeamId($tenant->id);
echo "Set Tenant to: " . $tenant->name . "\n";

// 3. Check Resources
$panel = \Filament\Facades\Filament::getPanel('app');
$resources = $panel->getResources();

echo "Registered Resources in 'app' panel:\n";
$foundPatient = false;
foreach ($resources as $r) {
    if (str_contains($r, 'PatientResource')) {
        echo " [FOUND] $r\n";
        $foundPatient = true;
    }
}

// 4. Check Authorization
$resourceClass = 'App\Filament\App\Resources\Patients\PatientResource';

if ($foundPatient || class_exists($resourceClass)) {
    echo "\nChecking Authorization for $resourceClass...\n";
    try {
        $canViewAny = $resourceClass::canViewAny();
        echo "canViewAny(): " . ($canViewAny ? 'YES' : 'NO') . "\n";
    } catch (\Exception $e) {
        echo "Error in canViewAny(): " . $e->getMessage() . "\n";
    }
} else {
    echo "\nPatientResource class not found or not registered.\n";
}

// 5. Gate Check Backup
echo "\nGate Check 'ViewAny:Patient' (using user->can): " . ($u->can('ViewAny:Patient') ? 'YES' : 'NO') . "\n";
