<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SystemTools extends Page
{
    protected string $view = 'filament.pages.system-tools';

    public static function getNavigationIcon(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-wrench-screwdriver';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System';
    }

    public function getTitle(): string
    {
        return 'System Tools';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('fixPermissions')
                ->label('Fix Database Permissions')
                ->icon('heroicon-m-key')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Repair Sequence Permissions')
                ->modalDescription('This will grant the necessary permissions for auto-incrementing IDs in PostgreSQL. Run this if you see errors like "permission denied for sequence".')
                ->action(function () {
                    try {
                        $user = config('database.connections.pgsql.username');
                        \Illuminate\Support\Facades\DB::statement("GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO \"$user\"");
                        
                        Notification::make()
                            ->title('Permissions fixed successfully!')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error fixing permissions')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('fixSchema')
                ->label('Fix Database Schema')
                ->icon('heroicon-m-table-cells')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Repair Database Schema')
                ->modalDescription('This will check for common schema mismatches (like tenant_id vs clinic_id) and attempt to fix them.')
                ->action(function () {
                    try {
                        $messages = [];

                        // 1. Check domains.tenant_id vs clinic_id
                        $hasTenantId = \Illuminate\Support\Facades\Schema::hasColumn('domains', 'tenant_id');
                        $hasClinicId = \Illuminate\Support\Facades\Schema::hasColumn('domains', 'clinic_id');

                        if ($hasTenantId && !$hasClinicId) {
                            \Illuminate\Support\Facades\DB::statement('ALTER TABLE domains RENAME COLUMN tenant_id TO clinic_id');
                            $messages[] = 'Column domains.tenant_id renamed to clinic_id.';
                        }

                        // 2. Sync Permissions Migrations (Mark as done if columns exist)
                        $permissionMigrations = [
                            '2026_01_15_130000_create_permission_tables' => 'permissions',
                            '2026_01_15_140000_add_clinic_id_to_permissions_tables' => 'roles',
                            '2026_01_21_173957_add_clinic_id_to_role_has_permissions_table' => 'role_has_permissions',
                        ];

                        foreach ($permissionMigrations as $migrationName => $tableName) {
                            $isMigrated = \Illuminate\Support\Facades\DB::table('migrations')->where('migration', $migrationName)->exists();
                            $tableExists = \Illuminate\Support\Facades\Schema::hasTable($tableName);
                            $columnExists = ($tableName === 'permissions') ? true : \Illuminate\Support\Facades\Schema::hasColumn($tableName, 'clinic_id');

                            if ($tableExists && $columnExists && !$isMigrated) {
                                $maxBatch = \Illuminate\Support\Facades\DB::table('migrations')->max('batch') ?? 0;
                                \Illuminate\Support\Facades\DB::table('migrations')->insert([
                                    'migration' => $migrationName,
                                    'batch' => $maxBatch + 1,
                                ]);
                                $messages[] = "Migration '$migrationName' marked as synced.";
                            }
                        }

                        if (empty($messages)) {
                            Notification::make()->title('Schema seems correct or no fixes needed.')->info()->send();
                        } else {
                            Notification::make()
                                ->title('Schema fixes applied!')
                                ->body(implode(' ', $messages))
                                ->success()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error fixing schema')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('runMigrations')
                ->label('Run Database Migrations')
                ->icon('heroicon-m-arrow-path')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Run Pending Migrations?')
                ->modalDescription('This will execute "php artisan migrate" in production. Use this to create missing tables or columns after an update.')
                ->action(function () {
                    try {
                        Artisan::call('migrate', ['--force' => true]);
                        Notification::make()
                            ->title('Migrations executed successfully!')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error running migrations')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('runSeeders')
                ->label('Run Database Seeders (Soft Reset)')
                ->icon('heroicon-m-play')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Run Seeders?')
                ->modalDescription('This will execute the database seeders and insert base data into your SaaS. Since it uses firstOrCreate, it should gracefully restore roles and seed missing components without deleting production data.')
                ->modalSubmitActionLabel('Yes, run them')
                ->action(function () {
                    try {
                        Artisan::call('db:seed', ['--force' => true]);
                        Notification::make()
                            ->title('Seeders executed successfully!')
                            ->body('The output was recorded in the system logs.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error executing seeders')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('hardReset')
                ->label('HARD RESET (Danger)')
                ->icon('heroicon-m-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('WARNING: PERMANENT DATA LOSS')
                ->modalDescription('THIS WILL DELETE ALL DATA IN THE DATABASE (migrate:fresh) AND START FROM ZERO. Only use this if you want to completely reset the production environment. Proceed?')
                ->modalSubmitActionLabel('I UNDERSTAND, WIPE ALL DATA')
                ->modalIcon('heroicon-o-exclamation-triangle')
                ->hidden(!auth()->user()->hasRole('super-admin'))
                ->action(function () {
                    // Verificación 1: Solo super-admin
                    if (!auth()->user()->hasRole('super-admin')) {
                        Notification::make()
                            ->title('Acceso Denegado')
                            ->body('Solo usuarios con rol super-admin pueden ejecutar un Hard Reset.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Verificación 2: No en producción
                    if (app()->environment('production')) {
                        Notification::make()
                            ->title('Operación Bloqueada')
                            ->body('Hard Reset está deshabilitado en producción por seguridad.')
                            ->danger()
                            ->send();
                        return;
                    }

                    try {
                        // Log de auditoría ANTES de ejecutar
                        Log::channel('audit')->critical(
                            'HARD RESET EXECUTED',
                            [
                                'user_id' => auth()->id(),
                                'user_email' => auth()->user()->email,
                                'user_name' => auth()->user()->name,
                                'timestamp' => now()->toIso8601String(),
                                'environment' => app()->environment(),
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->userAgent(),
                            ]
                        );

                        // 1. Fresh migrate (destructive)
                        Artisan::call('migrate:fresh', ['--force' => true]);
                        
                        // 2. Run seeders
                        Artisan::call('db:seed', ['--force' => true]);

                        // Log exitoso
                        Log::channel('audit')->info(
                            'HARD RESET COMPLETED',
                            [
                                'user_id' => auth()->id(),
                                'user_email' => auth()->user()->email,
                            ]
                        );

                        // Notificación por email a todos los super-admins
                        $superAdmins = User::role('super-admin')->get();
                        
                        foreach ($superAdmins as $admin) {
                            if ($admin->id !== auth()->id()) {  // No notificar al que ejecutó
                                Mail::raw(
                                    "ALERTA DE SEGURIDAD - HARD RESET EJECUTADO\n\n" .
                                    "Usuario: " . auth()->user()->name . " (" . auth()->user()->email . ")\n" .
                                    "Fecha: " . now()->toIso8601String() . "\n" .
                                    "IP: " . request()->ip() . "\n" .
                                    "Ambiente: " . app()->environment() . "\n\n" .
                                    "La base de datos ha sido completamente reiniciada.\n" .
                                    "Revise los logs de auditoría para más detalles.",
                                    function ($message) use ($admin) {
                                        $message
                                            ->to($admin->email)
                                            ->subject('🚨 ALERTA: Hard Reset Ejecutado en ' . config('app.name'));
                                    }
                                );
                            }
                        }

                        Notification::make()
                            ->title('Hard Reset Successful!')
                            ->body('The database was wiped and recreated with fresh seed data. Email notifications sent to all super-admins.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        // Log del error
                        Log::channel('audit')->error(
                            'HARD RESET FAILED',
                            [
                                'user_id' => auth()->id(),
                                'user_email' => auth()->user()->email,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                            ]
                        );

                        Notification::make()
                            ->title('Error during Hard Reset')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('fixDomains')
                ->label('Fix Dominios')
                ->icon('heroicon-s-globe-alt')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Fix de Dominios')
                ->modalDescription('Este script verificará y registrará el dominio clinicatest.dentalflow.digitalwebsolution.info si no existe.')
                ->action(function () {
                    try {
                        $output = [];
                        
                        $clinics = \Illuminate\Support\Facades\DB::table('tenants')->get();
                        $output[] = "Clínicas encontradas: " . $clinics->count();
                        
                        foreach ($clinics as $clinic) {
                            $output[] = "  - {$clinic->id}: {$clinic->name}";
                        }

                        $domains = \Illuminate\Support\Facades\DB::table('domains')->get();
                        $output[] = "\nDominios registrados: " . $domains->count();
                        
                        foreach ($domains as $domain) {
                            $output[] = "  - {$domain->domain} → {$domain->tenant_id}";
                        }

                        $targetDomain = 'clinicatest.dentalflow.digitalwebsolution.info';
                        $domainExists = \Illuminate\Support\Facades\DB::table('domains')
                            ->where('domain', $targetDomain)
                            ->first();

                        if ($domainExists) {
                            $output[] = "\n✅ El dominio '{$targetDomain}' YA existe";
                        } else {
                            $output[] = "\n⚠️ El dominio '{$targetDomain}' NO existe";
                            
                            $clinic = \Illuminate\Support\Facades\DB::table('tenants')->where('id', 'clinicatest')->first();
                            
                            if ($clinic) {
                                $output[] = "✅ Clínica 'clinicatest' encontrada";
                            } else {
                                $output[] = "⚠️ Clínica 'clinicatest' no existe, creando...";
                                \Illuminate\Support\Facades\DB::table('tenants')->insert([
                                    'id' => 'clinicatest',
                                    'name' => 'Clínica Test',
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                $output[] = "✅ Clínica creada";
                            }

                            $output[] = "Registrando dominio...";
                            \Illuminate\Support\Facades\DB::table('domains')->insert([
                                'id' => uniqid('dom_'),
                                'domain' => $targetDomain,
                                'tenant_id' => 'clinicatest',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            $output[] = "✅ Dominio registrado exitosamente";
                        }

                        Notification::make()
                            ->title('Fix de dominios completado')
                            ->success()
                            ->body(implode("\n", $output))
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error al aplicar fix')
                            ->danger()
                            ->body($e->getMessage())
                            ->send();
                    }
                }),

            Action::make('runMigrations')
                ->label('Ejecutar Migraciones')
                ->icon('heroicon-s-arrow-up-tray')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Ejecutar Migraciones')
                ->modalDescription('Esto ejecutará todas las migraciones pendientes en la base de datos central y en todos los tenants.')
                ->action(function () {
                    try {
                        $output = [];
                        
                        $output[] = "=== Migraciones Centrales ===";
                        $outputCentral = new \Symfony\Component\Console\Output\BufferedOutput();
                        $exitCode = \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true], $outputCentral);
                        $output[] = $outputCentral->fetch();
                        
                        if ($exitCode === 0) {
                            $output[] = "✅ Migraciones centrales completadas";
                        }

                        $output[] = "\n=== Migraciones en Tenants ===";
                        $outputTenants = new \Symfony\Component\Console\Output\BufferedOutput();
                        $exitCode2 = \Illuminate\Support\Facades\Artisan::call('tenants:migrate', ['--force' => true], $outputTenants);
                        $output[] = $outputTenants->fetch();
                        
                        if ($exitCode2 === 0) {
                            $output[] = "✅ Migraciones en tenants completadas";
                        }

                        Notification::make()
                            ->title('Migraciones completadas')
                            ->success()
                            ->body(implode("\n", $output))
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error en migraciones')
                            ->danger()
                            ->body($e->getMessage())
                            ->send();
                    }
                }),

            Action::make('checkInventories')
                ->label('Verificar Inventories')
                ->icon('heroicon-s-clipboard-document-check')
                ->color('info')
                ->action(function () {
                    try {
                        $columns = \Illuminate\Support\Facades\DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'inventories'");
                        $columnNames = array_column($columns, 'column_name');
                        
                        $output = ["Columnas en inventories:"];
                        $output[] = implode(', ', $columnNames);
                        
                        if (!in_array('price', $columnNames)) {
                            $output[] = "\n⚠️ Falta columna 'price'";
                            $output[] = "Ejecuta las migraciones para solucionar";
                        } else {
                            $output[] = "\n✅ Tabla inventories correcta";
                        }

                        Notification::make()
                            ->title('Verificación completada')
                            ->info()
                            ->body(implode("\n", $output))
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error en verificación')
                            ->danger()
                            ->body($e->getMessage())
                            ->send();
                    }
                }),
        ];
    }
}
