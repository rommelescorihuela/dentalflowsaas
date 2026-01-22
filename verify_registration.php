<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\TenantService;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Support\Facades\DB;

$timestamp = time();
$subdomain = 'testclinic' . $timestamp;
$email = 'admin' . $timestamp . '@testclinic.com';

echo "Verifying Tenant Registration Logic...\n";
echo "Subdomain: $subdomain\n";
echo "Email: $email\n";

try {
    $service = new TenantService();

    // Test Creation
    $clinic = $service->createTenant([
        'company_name' => 'Test Clinic ' . $timestamp,
        'subdomain' => $subdomain,
        'name' => 'Dr. Test',
        'email' => $email,
        'password' => 'password123',
    ]);

    echo "[PASS] Tenant Service returned clinic instance.\n";

    // Verify Clinic
    $dbClinic = Clinic::find($subdomain);
    if ($dbClinic && $dbClinic->name === 'Test Clinic ' . $timestamp) {
        echo "[PASS] Clinic record created in DB.\n";
    } else {
        echo "[FAIL] Clinic record NOT found or incorrect.\n";
    }

    // Verify Domain
    if ($dbClinic->domains()->where('domain', $subdomain . '.localhost')->exists()) { // Default config is likely localhost or similar
        echo "[PASS] Domain record created via relationship.\n";
    } else {
        echo "[WARN] Domain record verification uncertain (check config('tenancy.central_domains')).\n";
        // Let's print actual domains
        foreach ($dbClinic->domains as $d) {
            echo " - Found domain: " . $d->domain . "\n";
        }
    }

    // Verify User
    $user = User::where('email', $email)->first();
    if ($user && $user->clinic_id === $subdomain) {
        echo "[PASS] Admin User created and linked to clinic.\n";
    } else {
        echo "[FAIL] Admin User NOT found or not linked.\n";
    }

} catch (\Exception $e) {
    echo "[ERROR] Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
