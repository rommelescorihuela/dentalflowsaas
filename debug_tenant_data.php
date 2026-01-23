<?php

use App\Models\Clinic;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- Debugging Clinic Data Persistence ---\n";

// 1. Create or Get Clinic
$clinic = Clinic::first();
if (!$clinic) {
    echo "Creating new clinic...\n";
    $clinic = Clinic::create([
        'id' => 'debug-clinic-' . time(),
        'name' => 'Debug Clinic',
    ]);
}
echo "Using Clinic ID: " . $clinic->id . "\n";

// 2. Set Data via Explicit Array Update
echo "Setting schedule_start via 'data' array...\n";
$currentData = $clinic->data ?? [];
$currentData['schedule_start'] = '10:00';
$clinic->update(['data' => $currentData]);

// 3. Refresh from DB
echo "Refreshing from DB...\n";
$clinic->refresh();
echo "Clinic->data after refresh: " . json_encode($clinic->data) . "\n";

// 4. Fetch Fresh instance
echo "Fetching fresh instance...\n";
$fresh = Clinic::find($clinic->id);
echo "Fresh->data: " . json_encode($fresh->data) . "\n";

// 5. Initialize Tenancy
echo "Initializing Tenancy...\n";
tenancy()->initialize($clinic);
echo "tenant()->data: " . json_encode(tenant()->data) . "\n";

if (isset($clinic->data['schedule_start']) && $clinic->data['schedule_start'] === '10:00') {
    echo "✅ Success: Data persistence works.\n";
} else {
    echo "❌ Fail: Data persistence broken.\n";
}
