<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header('Content-Type: application/json');

echo json_encode([
    'host' => $_SERVER['HTTP_HOST'] ?? 'unknown',
    'central_domains' => config('tenancy.central_domains'),
    'app_url' => config('app.url'),
    'env' => config('app.env'),
    'is_central' => in_array($_SERVER['HTTP_HOST'] ?? '', config('tenancy.central_domains', [])),
]);
