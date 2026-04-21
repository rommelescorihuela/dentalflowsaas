<?php

// Script de diagnóstico para rutas de Filament con tenant
// Uso: php artisan test:routes

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;

class TestRoutesCommand extends Command
{
    protected $signature = 'test:routes';
    protected $description = 'Diagnosticar generación de rutas con tenant';

    public function handle()
    {
        $tenantId = 'clinic1';
        URL::defaults(['tenant' => $tenantId]);

        $this->info("=== TEST DE RUTAS ===");
        $this->line("Tenant ID: " . $tenantId);
        $this->line("URL Defaults: " . json_encode(URL::getDefaultParameters()));
        $this->newLine();

        try {
            $url = route('filament.app.pages.dashboard');
            $this->info("✅ Dashboard: " . $url);
        } catch (\Exception $e) {
            $this->error("❌ Dashboard: " . $e->getMessage());
        }

        try {
            $url = route('filament.app.auth.login');
            $this->info("✅ Login: " . $url);
        } catch (\Exception $e) {
            $this->error("❌ Login: " . $e->getMessage());
        }

        try {
            $url = route('filament.app.resources.patients.index');
            $this->info("✅ Patients: " . $url);
        } catch (\Exception $e) {
            $this->error("❌ Patients: " . $e->getMessage());
        }

        $this->newLine();
        $this->info("=== TEST COMPLETO ===");
    }
}