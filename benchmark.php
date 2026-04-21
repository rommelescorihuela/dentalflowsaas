<?php

$appUrl = 'http://127.0.0.1:8000';

define('GREEN', "\033[32m");
define('RED', "\033[31m");
define('YELLOW', "\033[33m");
define('BLUE', "\033[34m");
define('RESET', "\033[0m");

echo YELLOW . "=== DENTALFLOW SAAS BENCHMARK ===" . RESET . "\n";
echo "Target: $appUrl\n\n";

function measureUrl($name, $url, $expectedCode = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $start = microtime(true);
    $response = curl_exec($ch);
    $end = microtime(true);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ttfb = curl_getinfo($ch, CURLINFO_STARTTRANSFER_TIME);
    $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    
    curl_close($ch);
    
    $timeMs = round($totalTime * 1000, 2);
    $ttfbMs = round($ttfb * 1000, 2);
    
    $status = RED . "❌ Error" . RESET;
    if ($httpCode >= 200 && $httpCode < 300) {
        $status = GREEN . "✅ OK" . RESET;
    } elseif ($httpCode >= 300 && $httpCode < 400) {
        $status = BLUE . "↔ Redirect" . RESET;
    } elseif ($httpCode === 401 || $httpCode === 403) {
        $status = YELLOW . "🔒 Protected" . RESET;
    } elseif ($httpCode === 404) {
        $status = RED . "❓ Not Found" . RESET;
    }
    
    if ($expectedCode && $httpCode !== $expectedCode) {
        $status .= RED . " (Expected $expectedCode)" . RESET;
    }
    
    echo sprintf("%-35s %s (Total: %6.2fms | TTFB: %6.2fms)\n", 
        $name . ":", 
        $status, 
        $timeMs, 
        $ttfbMs
    );
    
    return ['code' => $httpCode, 'time' => $timeMs, 'ttfb' => $ttfbMs];
}

echo BLUE . "--- RUTAS PÚBLICAS ---" . RESET . "\n";
measureUrl("Landing Page", $appUrl . "/");
measureUrl("Registration", $appUrl . "/register");
measureUrl("Login (redirect)", $appUrl . "/login");
measureUrl("Register Success", $appUrl . "/register/success?tenant_id=test");

echo "\n" . BLUE . "--- PANEL ADMIN ---" . RESET . "\n";
measureUrl("Admin Panel", $appUrl . "/admin");
measureUrl("Admin Login", $appUrl . "/admin/login");

echo "\n" . BLUE . "--- PANEL CLÍNICA (APP) ---" . RESET . "\n";
measureUrl("App Panel (con tenant)", $appUrl . "/demo-clinic-a/app");
measureUrl("App Login", $appUrl . "/demo-clinic-a/app/login");

echo "\n" . BLUE . "--- MULTI-TENANT SUBDOMAINS ---" . RESET . "\n";
measureUrl("Subdomain Clinic A", "http://demo-clinic-a.localhost:8000/", null);
measureUrl("Subdomain Clinic B", "http://demo-clinic-b.localhost:8000/", null);

echo "\n" . BLUE . "--- PORTAL PACIENTE ---" . RESET . "\n";
measureUrl("Portal (sin auth)", $appUrl . "/demo-clinic-a/portal/1");
measureUrl("Portal Book", $appUrl . "/demo-clinic-a/portal/1/book");

echo "\n" . BLUE . "--- HEALTH CHECK ---" . RESET . "\n";
measureUrl("Health Check", $appUrl . "/up");

echo "\n" . YELLOW . "=== BENCHMARK COMPLETO ===" . RESET . "\n";

$results = [
    ['name' => 'Landing', 'url' => $appUrl . "/"],
    ['name' => 'Register', 'url' => $appUrl . "/register"],
    ['name' => 'Admin', 'url' => $appUrl . "/admin"],
    ['name' => 'App', 'url' => $appUrl . "/demo-clinic-a/app"],
    ['name' => 'Health', 'url' => $appUrl . "/up"],
];

$totalTime = 0;
$count = 0;
foreach ($results as $r) {
    $result = measureUrl($r['name'], $r['url']);
    $totalTime += $result['time'];
    $count++;
}

$avg = round($totalTime / $count, 2);
echo "\n" . GREEN . "Tiempo promedio: {$avg}ms" . RESET . "\n";

if ($avg < 100) {
    echo GREEN . "🚀 Excelente rendimiento" . RESET . "\n";
} elseif ($avg < 300) {
    echo YELLOW . "⚠️ Rendimiento aceptable" . RESET . "\n";
} else {
    echo RED . "⚠️ Rendimiento bajo - considerar optimización" . RESET . "\n";
}
