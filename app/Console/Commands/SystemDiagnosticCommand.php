<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use App\Models\Payment;
use App\Models\Budget;
use App\Services\TenantService;
use Illuminate\Support\Facades\URL;
use ReflectionMethod;

class SystemDiagnosticCommand extends Command
{
    protected $signature = 'diagnostic:all {--skip-tests : Saltar tests automatizados}';
    protected $description = 'Ejecutar diagnóstico completo del sistema DentalFlow';

    public function handle()
    {
        $this->info('=== DENTALFLOW SAAS DIAGNOSTIC ===');
        $this->info('Timestamp: ' . now()->toDateTimeString());
        $this->newLine();

        $startTotal = microtime(true);

        // 1. Health Check
        $this->healthCheck();

        // 2. Feature Verification
        $this->featureVerification();

        // 3. Benchmark
        $this->benchmark();

        // 4. Tests (opcional)
        if (!$this->option('skip-tests')) {
            $this->runTests();
        }

        $endTotal = microtime(true);
        $this->newLine();
        $this->info('=== DIAGNOSTIC COMPLETO ===');
        $this->info('Duración total: ' . round($endTotal - $startTotal, 2) . 's');
    }

    private function healthCheck()
    {
        $this->info('[1] SALUD DEL SISTEMA');
        $this->line('---');

        try {
            DB::connection()->getPdo();
            $this->info('  ✅ Base de datos: OK');
        } catch (\Exception $e) {
            $this->error('  ❌ Base de datos: ' . $e->getMessage());
        }

        $tenantCount = Clinic::count();
        $this->info("  ✅ Clínicas: {$tenantCount} activas");

        $userCount = User::count();
        $this->info("  ✅ Usuarios: {$userCount} registrados");
    }

    private function featureVerification()
    {
        $this->newLine();
        $this->info('[2] VERIFICACIÓN DE FEATURES');
        $this->line('---');

        // Self-Onboarding
        if (Schema::hasColumn('tenants', 'onboarding_step')) {
            $this->info('  ✅ Onboarding: OK');
        } else {
            $this->error('  ❌ Onboarding: FALTA columna');
        }

        // Patient Portal
        $clinic = Clinic::first();
        if ($clinic) {
            tenancy()->initialize($clinic);
            $component = new \App\Livewire\PatientPortal\BookAppointment();
            $component->patient = Patient::first() ?? Patient::create([
                'name' => 'Test Patient',
                'email' => 'test@test.com',
                'clinic_id' => $clinic->id,
                'phone' => '1234567890',
            ]);
            $component->selectedDate = now()->addDay()->format('Y-m-d');
            $component->loadTimeSlots();
            $slots = count($component->availableSlots);
            $this->info("  ✅ Patient Portal: {$slots} slots generados");
        }

        // BI Widgets
        try {
            $widget = new \App\Filament\App\Widgets\FinancialStatsOverview();
            $method = new ReflectionMethod($widget, 'getStats');
            $method->setAccessible(true);
            $stats = $method->invoke($widget);
            $this->info('  ✅ BI Dashboard: ' . count($stats) . ' KPIs');
        } catch (\Exception $e) {
            $this->error('  ❌ BI Dashboard: ' . $e->getMessage());
        }

        // Tenant Isolation
        $clinicA = Clinic::create(['id' => 'iso_a_' . time(), 'name' => 'Test A']);
        $clinicB = Clinic::create(['id' => 'iso_b_' . time(), 'name' => 'Test B']);

        tenancy()->initialize($clinicA);
        Patient::create(['name' => 'P A', 'email' => 'pa@test.com', 'clinic_id' => $clinicA->id, 'phone' => '1']);

        tenancy()->initialize($clinicB);
        $visible = Patient::all();

        if ($visible->contains('name', 'P A')) {
            $this->error('  ❌ Tenant Isolation: HAY FUGA DE DATOS');
        } else {
            $this->info('  ✅ Tenant Isolation: OK');
        }

        $clinicA->delete();
        $clinicB->delete();

        // Odontogram
        if (view()->exists('livewire.odontogram-v2')) {
            $this->info('  ✅ Odontogram: OK');
        } else {
            $this->error('  ❌ Odontogram: FALTA');
        }
    }

    private function benchmark()
    {
        $this->newLine();
        $this->info('[3] BENCHMARK');
        $this->line('---');

        $urls = [
            '/' => 'Landing',
            '/register' => 'Register',
            '/admin/login' => 'Admin Login',
            '/up' => 'Health',
        ];

        $times = [];
        foreach ($urls as $url => $name) {
            $ch = curl_init('http://127.0.0.1:8000' . $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $start = microtime(true);
            curl_exec($ch);
            $time = round((microtime(true) - $start) * 1000, 2);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $status = $code >= 200 && $code < 400 ? '✅' : ($code >= 300 ? '↔' : '❌');
            $this->line("  {$status} {$name}: {$code} ({$time}ms)");
            $times[] = $time;
        }

        $avg = round(array_sum($times) / count($times), 2);
        $this->info("  📊 Promedio: {$avg}ms");

        if ($avg < 100) {
            $this->info('  🚀 Rendimiento: Excelente');
        } elseif ($avg < 300) {
            $this->warn('  ⚠️ Rendimiento: Aceptable');
        } else {
            $this->error('  ⚠️ Rendimiento: Bajo');
        }
    }

    private function runTests()
    {
        $this->newLine();
        $this->info('[4] TESTS AUTOMATIZADOS');
        $this->line('---');

        $result = $this->call('test', ['--without-tty' => true]);
    }
}