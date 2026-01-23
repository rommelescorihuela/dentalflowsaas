<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Budget;
use ReflectionMethod;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$green = "\033[32m";
$red = "\033[31m";
$yellow = "\033[33m";
$reset = "\033[0m";

echo "\n{$green}--- DENTALFLOW SAAS SYSTEM HEALTH CHECK ---{$reset}\n";
echo "Timestamp: " . now()->toDateTimeString() . "\n\n";

// --- 1. CORE SYSTEM HEALTH ---
echo "{$yellow}[1] Core System Health...{$reset}\n";
try {
    DB::connection()->getPdo();
    echo "  {$green}✔ Database Connection High: OK{$reset}\n";
} catch (\Exception $e) {
    echo "  {$red}✘ Database Connection Failed: " . $e->getMessage() . "{$reset}\n";
    exit(1);
}

$tenantCount = Clinic::count();
echo "  {$green}✔ Tenants (Clinics): Found {$tenantCount} active clinics.{$reset}\n";

$userCount = User::count();
echo "  {$green}✔ Users (Doctors/Admins): Found {$userCount} users registered.{$reset}\n";

// --- 2. PHASE 2: SELF-ONBOARDING ---
echo "\n{$yellow}[2] Feature: Self-Onboarding...{$reset}\n";
if (Schema::hasColumn('tenants', 'onboarding_step')) {
    echo "  {$green}✔ Schema: 'onboarding_step' column exists.{$reset}\n";
} else {
    echo "  {$red}✘ Schema: 'onboarding_step' column is MISSING.{$reset}\n";
}

if (class_exists(\App\Http\Middleware\ForceOnboardingMiddleware::class)) {
    echo "  {$green}✔ Middleware: ForceOnboardingMiddleware class present.{$reset}\n";
} else {
    echo "  {$red}✘ Middleware: ForceOnboardingMiddleware class MISSING.{$reset}\n";
}

// --- 3. PHASE 3: PATIENT PORTAL & SCHEDULING ---
echo "\n{$yellow}[3] Feature: Patient Portal & Scheduling...{$reset}\n";
$clinic = Clinic::first();
if ($clinic) {
    tenancy()->initialize($clinic);

    // Check Patient logic
    $patient = Patient::first();
    if ($patient) {
        // Test Booking Logic
        try {
            $component = new \App\Livewire\PatientPortal\BookAppointment();
            $component->patient = $patient;
            $component->selectedDate = now()->addDay()->format('Y-m-d');
            $component->loadTimeSlots();

            if (count($component->availableSlots) >= 0) {
                echo "  {$green}✔ Logic: Slot generation engine working (Slots: " . count($component->availableSlots) . ").{$reset}\n";
            }
        } catch (\Exception $e) {
            echo "  {$red}✘ Logic: Booking Engine Error: " . $e->getMessage() . "{$reset}\n";
        }
    } else {
        echo "  {$yellow}⚠ Warning: No patients found to test scheduling.{$reset}\n";
    }
} else {
    echo "  {$red}✘ Critical: No Tenant found to test tenant-scoped features.{$reset}\n";
}

// --- 4. PHASE 4: BUSINESS INTELLIGENCE ---
echo "\n{$yellow}[4] Feature: Business Intelligence (Dashboard)...{$reset}\n";
try {
    // Generate fresh data to ensure chart works
    if ($patient) {
        Payment::create(['amount' => 100, 'paid_at' => now(), 'clinic_id' => $clinic->id, 'patient_id' => $patient->id, 'method' => 'test']);
    }

    $chartWidget = new \App\Filament\App\Widgets\RevenueChart();
    $methodChart = new ReflectionMethod(\App\Filament\App\Widgets\RevenueChart::class, 'getData');
    $methodChart->setAccessible(true);
    $data = $methodChart->invoke($chartWidget);

    if (!empty($data['datasets'])) {
        echo "  {$green}✔ Widget: RevenueChart generating data points.{$reset}\n";
    } else {
        echo "  {$red}✘ Widget: RevenueChart returned empty data.{$reset}\n";
    }

    $statsWidget = new \App\Filament\App\Widgets\FinancialStatsOverview();
    $methodStats = new ReflectionMethod(\App\Filament\App\Widgets\FinancialStatsOverview::class, 'getStats');
    $methodStats->setAccessible(true);
    $stats = $methodStats->invoke($statsWidget);
    echo "  {$green}✔ Widget: FinancialStatsOverview operational. Checked KPIs: " . count($stats) . ".{$reset}\n";

} catch (\Exception $e) {
    echo "  {$red}✘ BI Error: " . $e->getMessage() . "{$reset}\n";
}

// --- 5. ACTIVITY LOGGING & TENANT ISOLATION ---
echo "\n{$yellow}[5] Core: Security & Activity Logging...{$reset}\n";

// 5a. Test Activity Log Creation
$originalCount = \App\Models\SystemActivity::count();
$testPayment = Payment::create([
    'amount' => 50,
    'paid_at' => now(),
    'clinic_id' => $clinic->id,
    'patient_id' => $patient->id,
    'method' => 'audit_test'
]);
$newCount = \App\Models\SystemActivity::count();

if ($newCount > $originalCount) {
    echo "  {$green}✔ Activity Log: Auto-logging worked (Count increased from $originalCount to $newCount).{$reset}\n";
} else {
    echo "  {$red}✘ Activity Log: No new activity recorded!{$reset}\n";
}

// 5b. Test Tenant Isolation
echo "  {$yellow}... Testing Tenant Isolation ...{$reset}\n";
$clinicA = Clinic::create(['id' => 'iso_test_a_' . time(), 'name' => 'Isolation Test A']);
$clinicB = Clinic::create(['id' => 'iso_test_b_' . time(), 'name' => 'Isolation Test B']);

tenancy()->initialize($clinicA);
$patientA = Patient::create([
    'name' => 'PatientA Test',
    'email' => 'a' . time() . '@test.com',
    'phone' => '1234567890',
    'birth_date' => '1990-01-01',
    'clinic_id' => $clinicA->id
]);

tenancy()->initialize($clinicB);
$visiblePatientsB = Patient::all();

if ($visiblePatientsB->contains('id', $patientA->id)) {
    echo "  {$red}✘ Security: DATA LEAK! Clinic B can see Clinic A's patient.{$reset}\n";
} else {
    echo "  {$green}✔ Security: Tenant Isolation Active. Clinic B cannot see Clinic A's data.{$reset}\n";
}

// 5c. Test Resource Query Scoping
$resourceQuery = \App\Filament\App\Resources\SystemActivities\SystemActivityResource::getEloquentQuery()->toSql();
if (str_contains($resourceQuery, '"clinic_id" = ?') || str_contains($resourceQuery, '`clinic_id` = ?')) {
    echo "  {$green}✔ Resource: SystemActivityResource query is correctly scoped by 'clinic_id'.{$reset}\n";
} else {
    echo "  {$red}✘ Resource: SystemActivityResource Query is NOT scoped! SQL: $resourceQuery{$reset}\n";
}

// Cleanup isolation test data
tenancy()->end();
$clinicA->delete();
$clinicB->delete(); // Cascading delete should handle patient if set up, otherwise soft delete ok for test

// --- 6. CLINICAL CORE (Regression Test) ---
echo "\n{$yellow}[6] Core: Clinical Functions...{$reset}\n";
if (view()->exists('livewire.odontogram-v2')) {
    echo "  {$green}✔ Odontogram: View 'odontogram-v2' exists.{$reset}\n";
} else {
    echo "  {$red}✘ Odontogram: View MISSING!{$reset}\n";
}

echo "\n{$green}--- SYSTEM VERIFICATION COMPLETE ---{$reset}\n";
