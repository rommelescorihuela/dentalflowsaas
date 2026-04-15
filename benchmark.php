<?php

// Performance Benchmark Script for DentalFlow SaaS
$appUrl = 'http://127.0.0.1:8000';

// Colors for CLI output
$green = "\033[32m";
$red = "\033[31m";
$yellow = "\033[33m";
$reset = "\033[0m";

echo "{$yellow}--- DENTALFLOW SAAS BENCHMARK ---{$reset}\n";
echo "Target URL: $appUrl\n\n";

function measureUrl($name, $url) {
    global $green, $red, $yellow, $reset;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Don't follow redirects so we can measure the initial response time
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); 
    curl_setopt($ch, CURLOPT_HEADER, false);

    $start = microtime(true);
    $response = curl_exec($ch);
    $end = microtime(true);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ttfb = curl_getinfo($ch, CURLINFO_STARTTRANSFER_TIME);
    $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    
    curl_close($ch);
    
    $timeMs = round($totalTime * 1000, 2);
    $ttfbMs = round($ttfb * 1000, 2);
    
    if ($httpCode >= 200 && $httpCode < 400) {
         echo "$name: {$green}$httpCode OK{$reset} (Total: {$timeMs}ms | TTFB: {$ttfbMs}ms)\n";
         if ($timeMs > 500) {
             echo "  {$red}⚠ Warning: Response time is over 500ms. Consider caching or query optimization.{$reset}\n";
         }
    } else {
         echo "$name: {$red}Error $httpCode{$reset} (Total: {$timeMs}ms)\n";
    }
}

// 1. Base Domain
measureUrl("Landing Page (/)", $appUrl . "/");

// 2. Registration Page
measureUrl("Registration (/register)", $appUrl . "/register");

// 3. Login Redirect (Filament Admin usually handles this)
measureUrl("Admin Login Redirect (/login/admin)", $appUrl . "/app/login");

// 4. Portal Check (Assuming an ID)
measureUrl("Patient Portal Access", $appUrl . "/1/portal/1");

echo "\n{$green}Benchmark Complete.{$reset}\n";
