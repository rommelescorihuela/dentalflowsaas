<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class ProductionFix extends Page
{
    protected string $view = 'filament.pages.production-fix';

    public ?string $lastOutput = null;
    public bool $fixApplied = false;

    public static function getNavigationIcon(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-wrench-screwdriver';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System';
    }

    public static function getNavigationLabel(): string
    {
        return 'Production Fix';
    }

    public function getTitle(): string
    {
        return 'Production Fix Tools';
    }

    public function fixDomains(): void
    {
        try {
            $output = [];
            
            $clinics = DB::table('tenants')->get();
            $output[] = "Clínicas encontradas: " . $clinics->count();
            
            foreach ($clinics as $clinic) {
                $output[] = "  - {$clinic->id}: {$clinic->name}";
            }

            $domains = DB::table('domains')->get();
            $output[] = "\nDominios registrados: " . $domains->count();
            
            foreach ($domains as $domain) {
                $output[] = "  - {$domain->domain} → {$domain->tenant_id}";
            }

            $targetDomain = 'clinicatest.dentalflow.digitalwebsolution.info';
            $domainExists = DB::table('domains')
                ->where('domain', $targetDomain)
                ->first();

            if ($domainExists) {
                $output[] = "\n✅ El dominio '{$targetDomain}' YA existe";
                $this->fixApplied = true;
            } else {
                $output[] = "\n⚠️ El dominio '{$targetDomain}' NO existe";
                
                $clinic = DB::table('tenants')->where('id', 'clinicatest')->first();
                
                if ($clinic) {
                    $output[] = "✅ Clínica 'clinicatest' encontrada";
                } else {
                    $output[] = "⚠️ Clínica 'clinicatest' no existe, creando...";
                    DB::table('tenants')->insert([
                        'id' => 'clinicatest',
                        'name' => 'Clínica Test',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $output[] = "✅ Clínica creada";
                }

                $output[] = "Registrando dominio...";
                DB::table('domains')->insert([
                    'id' => uniqid('dom_'),
                    'domain' => $targetDomain,
                    'tenant_id' => 'clinicatest',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $output[] = "✅ Dominio registrado exitosamente";
                $this->fixApplied = true;
            }

            $this->lastOutput = implode("\n", $output);
            
            Notification::make()
                ->title('Fix de dominios completado')
                ->success()
                ->send();

        } catch (\Exception $e) {
            $this->lastOutput = "ERROR: " . $e->getMessage();
            
            Notification::make()
                ->title('Error al aplicar fix')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    public function runMigrations(): void
    {
        try {
            $output = [];
            
            $output[] = "=== Migraciones Centrales ===";
            $outputCentral = new BufferedOutput();
            $exitCode = Artisan::call('migrate', ['--force' => true], $outputCentral);
            $output[] = $outputCentral->fetch();
            
            if ($exitCode === 0) {
                $output[] = "✅ Migraciones centrales completadas";
            }

            $output[] = "\n=== Migraciones en Tenants ===";
            $outputTenants = new BufferedOutput();
            $exitCode2 = Artisan::call('tenants:migrate', ['--force' => true], $outputTenants);
            $output[] = $outputTenants->fetch();
            
            if ($exitCode2 === 0) {
                $output[] = "✅ Migraciones en tenants completadas";
            }

            $this->lastOutput = implode("\n", $output);
            
            Notification::make()
                ->title('Migraciones completadas')
                ->success()
                ->send();

        } catch (\Exception $e) {
            $this->lastOutput = "ERROR: " . $e->getMessage();
            
            Notification::make()
                ->title('Error en migraciones')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    public function checkInventories(): void
    {
        try {
            $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'inventories'");
            $columnNames = array_column($columns, 'column_name');
            
            $output = ["Columnas en inventories:"];
            $output[] = implode(', ', $columnNames);
            
            if (!in_array('price', $columnNames)) {
                $output[] = "\n⚠️ Falta columna 'price'";
                $output[] = "Ejecuta las migraciones para solucionar";
            } else {
                $output[] = "\n✅ Tabla inventories correcta";
            }

            $this->lastOutput = implode("\n", $output);
            
            Notification::make()
                ->title('Verificación completada')
                ->info()
                ->send();

        } catch (\Exception $e) {
            $this->lastOutput = "ERROR: " . $e->getMessage();
            
            Notification::make()
                ->title('Error en verificación')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('fixDomains')
                ->label('Fix Dominios')
                ->icon('heroicon-o-globe-alt')
                ->color('primary')
                ->action('fixDomains')
                ->requiresConfirmation()
                ->modalHeading('Fix de Dominios')
                ->modalDescription('Este script verificará y registrará el dominio clinicatest.dentalflow.digitalwebsolution.info si no existe.'),
            
            Action::make('runMigrations')
                ->label('Ejecutar Migraciones')
                ->icon('heroicon-o-arrow-trending')
                ->color('success')
                ->action('runMigrations')
                ->requiresConfirmation()
                ->modalHeading('Ejecutar Migraciones')
                ->modalDescription('Esto ejecutará todas las migraciones pendientes en la base de datos central y en todos los tenants.'),
            
            Action::make('checkInventories')
                ->label('Verificar Inventories')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info')
                ->action('checkInventories'),
        ];
    }
}
