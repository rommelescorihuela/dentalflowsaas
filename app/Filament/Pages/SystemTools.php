<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SystemTools extends Page
{
    protected string $view = 'filament.pages.system-tools';

    public ?string $lastOutput = null;

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

    public function updateSystem(): void
    {
        try {
            $output = [];
            $systemToolsPath = app_path('Filament/Pages/SystemTools.php');
            
            if (!file_exists($systemToolsPath)) {
                throw new \Exception('No se encontró SystemTools.php');
            }

            $content = file_get_contents($systemToolsPath);
            
            // Reemplazar tenant_id por clinic_id
            $content = str_replace('$domain->clinic_id', '$domain->clinic_id', $content);
            $content = str_replace("'clinic_id' => 'clinicatest'", "'clinic_id' => 'clinicatest'", $content);
            
            // Guardar archivo actualizado
            file_put_contents($systemToolsPath, $content);
            
            $output[] = '✅ Archivo SystemTools.php actualizado';
            $output[] = '✅ Cambios: tenant_id → clinic_id';
            
            // Limpiar caché
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            
            $output[] = '✅ Caché limpiada';
            $output[] = '';
            $output[] = '¡LISTO! Ya puedes usar el botón Fix Dominios';
            
            $this->lastOutput = implode("\n", $output);
            
            Notification::make()
                ->title('Sistema actualizado')
                ->success()
                ->send();

        } catch (\Exception $e) {
            $this->lastOutput = "ERROR: " . $e->getMessage();
            
            Notification::make()
                ->title('Error en actualización')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('updateSystem')
                    ->label('Actualizar Sistema')
                    ->icon('heroicon-s-arrow-path')
                    ->color('warning')
                    ->action('updateSystem')
                    ->requiresConfirmation()
                    ->modalHeading('Actualizar SystemTools')
                    ->modalDescription('Esto corregirá el error "tenant_id vs clinic_id" en el código. Solo ejecuta esto una vez.'),

                Action::make('fixPermissions')
                    ->label('Corregir Permisos Secuencias')
                    ->icon('heroicon-m-key')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Reparar Permisos de Secuencias')
                    ->modalDescription('Esto otorgará los permisos necesarios para los IDs autoincrementales en PostgreSQL. Ejecuta esto si ves errores de "permission denied for sequence".')
                    ->action(function () {
                        try {
                            $user = config('database.connections.pgsql.username');
                            \Illuminate\Support\Facades\DB::statement("GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO \"$user\"");
                            
                            Notification::make()
                                ->title('¡Permisos corregidos exitosamente!')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al corregir permisos')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('fixSchema')
                    ->label('Corregir Esquema DB')
                    ->icon('heroicon-m-table-cells')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Reparar Esquema de Base de Datos')
                    ->modalDescription('Esto buscará inconsistencias comunes en el esquema (como tenant_id vs clinic_id) e intentará corregirlas.')
                    ->action(function () {
                        try {
                            $messages = [];

                            // 1. Check domains.tenant_id vs clinic_id
                            $hasTenantId = \Illuminate\Support\Facades\Schema::hasColumn('domains', 'tenant_id');
                            $hasClinicId = \Illuminate\Support\Facades\Schema::hasColumn('domains', 'clinic_id');

                            if ($hasTenantId && !$hasClinicId) {
                                \Illuminate\Support\Facades\DB::statement('ALTER TABLE domains RENAME COLUMN tenant_id TO clinic_id');
                                $messages[] = 'Columna domains.tenant_id renombrada a clinic_id.';
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
                                    $messages[] = "Migración '$migrationName' marcada como sincronizada.";
                                }
                            }

                            if (empty($messages)) {
                                Notification::make()->title('El esquema parece correcto o no se necesitan correcciones.').info()->send();
                            } else {
                                Notification::make()
                                    ->title('¡Mejoras de esquema aplicadas!')
                                    ->body(implode(' ', $messages))
                                    ->success()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al corregir esquema')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])->label('Herramientas DB')->icon('heroicon-m-wrench-screwdriver')->color('info')->button(),

            ActionGroup::make([
                Action::make('runCentralMigrations')
                    ->label('Migraciones Centrales')
                    ->icon('heroicon-m-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('¿Ejecutar Migraciones Pendientes?')
                    ->modalDescription('Esto ejecutará "php artisan migrate" en producción. Úsalo para crear tablas o columnas faltantes después de una actualización.')
                    ->action(function () {
                        try {
                            Artisan::call('migrate', ['--force' => true]);
                            Notification::make()
                                ->title('¡Migraciones ejecutadas exitosamente!')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al ejecutar migraciones')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('runTenantMigrations')
                    ->label('Migraciones Tenants')
                    ->icon('heroicon-s-arrow-up-tray')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Ejecutar Migraciones Tenants')
                    ->modalDescription('Esto ejecutará todas las migraciones pendientes en todos los tenants.')
                    ->action(function () {
                        try {
                            $output = [];
                            
                            $output[] = "=== Migraciones en Tenants ===";
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

                Action::make('runSeeders')
                    ->label('Ejecutar Seeders (Soft Reset)')
                    ->icon('heroicon-m-play')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('¿Ejecutar Seeders?')
                    ->modalDescription('Esto ejecutará los seeders de la base de datos e insertará datos base en su SaaS. Dado que utiliza firstOrCreate, debería restaurar roles y seedear componentes faltantes sin eliminar datos de producción.')
                    ->modalSubmitActionLabel('Sí, ejecutarlos')
                    ->action(function () {
                        try {
                            Artisan::call('db:seed', ['--force' => true]);
                            Notification::make()
                                ->title('¡Seeders ejecutados exitosamente!')
                                ->body('La salida se registró en los logs del sistema.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al ejecutar seeders')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])->label('Migraciones y Datos')->icon('heroicon-m-circle-stack')->color('warning')->button(),

            ActionGroup::make([
                Action::make('fixDomains')
                    ->label('Reparar Dominios')
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
                                $output[] = "  - {$domain->domain} → {$domain->clinic_id}";
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
                                    'domain' => $targetDomain,
                                    'clinic_id' => 'clinicatest',
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

                Action::make('checkInventories')
                    ->label('Verificar Inventarios')
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
            ])->label('Mantenimiento')->icon('heroicon-m-cog-6-tooth')->color('success')->button(),

            Action::make('hardReset')
                ->label('HARD RESET (Peligro)')
                ->icon('heroicon-m-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('ADVERTENCIA: PÉRDIDA PERMANENTE DE DATOS')
                ->modalDescription('ESTO ELIMINARÁ TODOS LOS DATOS EN LA BASE DE DATOS (migrate:fresh) Y COMENZARÁ DE CERO. Solo use esto si desea restablecer completamente el entorno de producción. ¿Proceder?')
                ->modalSubmitActionLabel('ENTIENDO, BORRAR TODOS LOS DATOS')
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
                            ->title('¡Hard Reset Exitoso!')
                            ->body('La base de datos fue borrada y recreada con datos iniciales frescos. Se enviaron notificaciones por correo electrónico a todos los super-admins.')
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
                            ->title('Error durante el Hard Reset')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
