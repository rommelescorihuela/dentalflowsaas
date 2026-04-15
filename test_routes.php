<?php

use Illuminate\Support\Facades\URL;

// Simulate segment identification
$tenantId = 'clinic1';
URL::defaults(['tenant' => $tenantId]);

echo "Tenant ID: " . $tenantId . "\n";
echo "URL Defaults: " . json_encode(URL::getDefaultParameters()) . "\n";

try {
    echo "Dashboard Route: " . route('filament.app.pages.dashboard') . "\n";
} catch (\Exception $e) {
    echo "Dashboard Route Error: " . $e->getMessage() . "\n";
}

try {
    echo "Login Route: " . route('filament.app.auth.login') . "\n";
} catch (\Exception $e) {
    echo "Login Route Error: " . $e->getMessage() . "\n";
}
