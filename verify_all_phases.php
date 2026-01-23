<?php

use Illuminate\Support\Facades\Schema;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Budget;
use ReflectionMethod;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- Starting System Verification ---\n";

// --- PHASE 2: SELF-ONBOARDING ---
echo "\n[Phase 2] Verifying Self-Onboarding...\n";

// 1. Check DB Column
if (Schema::hasColumn('tenants', 'onboarding_step')) {
    echo "✅ Database: Column 'onboarding_step' exists in 'tenants' table.\n";
} else {
    echo "❌ Database: Column 'onboarding_step' MISSING in 'tenants' table.\n";
}

// 2. Check Middleware
if (class_exists(\App\Http\Middleware\ForceOnboardingMiddleware::class)) {
    echo "✅ Class: ForceOnboardingMiddleware exists.\n";
} else {
    echo "❌ Class: ForceOnboardingMiddleware MISSING.\n";
}

// --- PHASE 3: SELF-SCHEDULING ---
echo "\n[Phase 3] Verifying Self-Scheduling Logic...\n";

// Initialize Tenancy
$clinic = Clinic::first();
if (!$clinic) {
    echo "⚠️ No clinic found. Skipping logic tests.\n";
    exit;
}
echo "ℹ️ Testing with Clinic: {$clinic->name} (ID: {$clinic->id})\n";
tenancy()->initialize($clinic);

// Get Patient
$patient = Patient::first();
if (!$patient) {
    $patient = Patient::create([
        'name' => 'Test Patient',
        'email' => 'test@test.com',
        // 'status' => 'active', 
        'clinic_id' => $clinic->id
    ]);
}

// Test BookAppointment Logic
try {
    $component = new \App\Livewire\PatientPortal\BookAppointment();
    $component->patient = $patient;
    $component->selectedDate = now()->addDay()->format('Y-m-d'); // Tomorrow
    $component->loadTimeSlots();

    if (count($component->availableSlots) > 0) {
        echo "✅ Logic: Slots generated successfully for {$component->selectedDate}.\n";
        echo "   Sample slots: " . implode(', ', array_slice($component->availableSlots, 0, 5)) . "...\n";
    } else {
        echo "⚠️ Logic: No slots generated (Check business hours logic).\n";
    }
} catch (Exception $e) {
    echo "❌ Logic: BookAppointment Error - " . $e->getMessage() . "\n";
}


// --- PHASE 3.5: CONFIGURABLE SCHEDULES ---
echo "\n[Phase 3.5] Verifying Custom Schedule Logic...\n";

// Use explicit set and save
$newData = array_merge($clinic->data ?? [], [
    'schedule_start' => '10:00',
    'schedule_end' => '14:00'
]);
$clinic->data = $newData;
$clinic->save();
$clinic->refresh();
tenancy()->initialize($clinic);

echo "DEBUG: Tenant Data Payload (via tenant()): " . json_encode(tenant()->data) . "\n";

try {
    $component = new \App\Livewire\PatientPortal\BookAppointment();
    $component->patient = $patient;
    $component->selectedDate = now()->addDay()->format('Y-m-d');
    $component->loadTimeSlots();

    $firstSlot = $component->availableSlots[0] ?? null;
    $lastSlot = end($component->availableSlots) ?: null;

    echo "ℹ️ Custom Schedule set to: 10:00 - 14:00\n";
    echo "   First Slot Generated: $firstSlot\n";
    echo "   Last Slot Generated: $lastSlot\n";

    if ($firstSlot === '10:00' && $lastSlot === '13:30') {
        echo "✅ Logic: Custom schedule respected properly.\n";
    } else {
        echo "❌ Logic: Custom schedule FAILED. Expected 10:00 start, got $firstSlot.\n";
    }

} catch (Exception $e) {
    echo "❌ Logic: Custom Schedule Error - " . $e->getMessage() . "\n";
}


// --- PHASE 4: BUSINESS INTELLIGENCE ---
echo "\n[Phase 4] Verifying BI Dashboard Widgets...\n";

// Seed some data for stats
Payment::create(['amount' => 150.00, 'paid_at' => now(), 'clinic_id' => $clinic->id, 'patient_id' => $patient->id, 'method' => 'cash']);
Budget::create(['total' => 500.00, 'status' => 'accepted', 'clinic_id' => $clinic->id, 'patient_id' => $patient->id]);

// Test FinancialStatsOverview
try {
    $statsWidget = new \App\Filament\App\Widgets\FinancialStatsOverview();
    $method = new ReflectionMethod(\App\Filament\App\Widgets\FinancialStatsOverview::class, 'getStats');
    $method->setAccessible(true);
    $stats = $method->invoke($statsWidget);

    echo "✅ Widget: FinancialStatsOverview calculated stats:\n";
    foreach ($stats as $stat) {
        echo "   - " . $stat->getLabel() . ": " . $stat->getValue() . " (" . $stat->getDescription() . ")\n";
    }
} catch (Exception $e) {
    echo "❌ Widget: FinancialStatsOverview Error - " . $e->getMessage() . "\n";
}

// Test RevenueChart
try {
    $chartWidget = new \App\Filament\App\Widgets\RevenueChart();
    $methodChart = new ReflectionMethod(\App\Filament\App\Widgets\RevenueChart::class, 'getData');
    $methodChart->setAccessible(true);
    $data = $methodChart->invoke($chartWidget);

    $datasets = $data['datasets'] ?? [];
    if (count($datasets) > 0) {
        $points = count($datasets[0]['data'] ?? []);
        echo "✅ Widget: RevenueChart generated data points: {$points} (Last 12 months).\n";
    } else {
        echo "❌ Widget: RevenueChart returned empty datasets.\n";
    }
} catch (Exception $e) {
    echo "❌ Widget: RevenueChart Error - " . $e->getMessage() . "\n";
}

echo "\n--- Verification Complete ---\n";
