<?php

namespace App\Filament\Pages;

use App\Models\Inventory;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Output\BufferedOutput;

class SystemTools extends Page
{
    protected string $view = 'filament.pages.system-tools';

    public ?string $lastOutput = null;

    protected static ?string $navigationLabel = 'Herramientas del Sistema';

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return 'heroicon-o-wrench-screwdriver';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Sistema';
    }

    public function getTitle(): string
    {
        return 'Herramientas del Sistema';
    }

    protected function runCommand(string $command, array $params = []): string
    {
        $output = new BufferedOutput;
        $output->writeln("\$ php artisan {$command}");
        $output->writeln(str_repeat('-', 50));

        try {
            Artisan::call($command, $params, $output);
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }

        $output->writeln(str_repeat('-', 50));

        return $output->fetch();
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('fixPermissions')
                    ->label('Corregir Permisos Secuencias')
                    ->icon('heroicon-m-key')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Reparar Permisos de Secuencias')
                    ->modalDescription('Otorga permisos para IDs autoincrementales en PostgreSQL.')
                    ->action(function () {
                        $out = [];
                        $out[] = '$ php artisan fix:sequences';
                        $out[] = str_repeat('-', 50);

                        try {
                            $user = config('database.connections.pgsql.username');
                            DB::statement("GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO \"$user\"");
                            $out[] = "✅ Permisos otorgados a '{$user}' en todas las secuencias.";
                            $out[] = str_repeat('-', 50);

                            $this->lastOutput = implode("\n", $out);

                            Notification::make()
                                ->title('Permisos corregidos')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            $out[] = "ERROR: {$e->getMessage()}";
                            $out[] = str_repeat('-', 50);
                            $this->lastOutput = implode("\n", $out);

                            Notification::make()
                                ->title('Error al corregir permisos')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('fixSchema')
                    ->label('Corregir Esquema BD')
                    ->icon('heroicon-m-table-cells')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Reparar Esquema de Base de Datos')
                    ->modalDescription('Busca y corrige inconsistencias en el esquema (tenant_id vs clinic_id, migraciones).')
                    ->action(function () {
                        $out = [];
                        $out[] = '$ php artisan fix:schema';
                        $out[] = str_repeat('-', 50);

                        try {
                            $hasTenantId = Schema::hasColumn('domains', 'tenant_id');
                            $hasClinicId = Schema::hasColumn('domains', 'clinic_id');

                            if ($hasTenantId && ! $hasClinicId) {
                                DB::statement('ALTER TABLE domains RENAME COLUMN tenant_id TO clinic_id');
                                $out[] = '✅ domains.tenant_id → clinic_id';
                            } else {
                                $out[] = 'ℹ️  domains: sin cambios necesarios';
                            }

                            $permissionMigrations = [
                                '2026_01_15_130000_create_permission_tables' => 'permissions',
                                '2026_01_15_140000_add_clinic_id_to_permissions_tables' => 'roles',
                                '2026_01_21_173957_add_clinic_id_to_role_has_permissions_table' => 'role_has_permissions',
                            ];

                            foreach ($permissionMigrations as $migrationName => $tableName) {
                                $isMigrated = DB::table('migrations')->where('migration', $migrationName)->exists();
                                $tableExists = Schema::hasTable($tableName);
                                $columnExists = ($tableName === 'permissions') ? true : Schema::hasColumn($tableName, 'clinic_id');

                                if ($tableExists && $columnExists && ! $isMigrated) {
                                    $maxBatch = DB::table('migrations')->max('batch') ?? 0;
                                    DB::table('migrations')->insert([
                                        'migration' => $migrationName,
                                        'batch' => $maxBatch + 1,
                                    ]);
                                    $out[] = "✅ Migración '{$migrationName}' sincronizada";
                                } else {
                                    $out[] = "ℹ️  Migración '{$migrationName}': OK";
                                }
                            }

                            $out[] = str_repeat('-', 50);
                            $this->lastOutput = implode("\n", $out);

                            Notification::make()
                                ->title('Esquema verificado')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            $out[] = "ERROR: {$e->getMessage()}";
                            $out[] = str_repeat('-', 50);
                            $this->lastOutput = implode("\n", $out);

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
                    ->modalDescription('Ejecuta php artisan migrate en todas las bases de datos.')
                    ->action(function () {
                        $this->lastOutput = $this->runCommand('migrate', ['--force' => true]);

                        Notification::make()
                            ->title('Migraciones ejecutadas')
                            ->success()
                            ->send();
                    }),

                Action::make('runTenantMigrations')
                    ->label('Migraciones de Clínicas')
                    ->icon('heroicon-s-arrow-up-tray')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Ejecutar Migraciones de Clínicas')
                    ->modalDescription('Ejecuta las migraciones pendientes en todas las clínicas.')
                    ->action(function () {
                        $this->lastOutput = $this->runCommand('tenants:migrate', ['--force' => true]);

                        Notification::make()
                            ->title('Migraciones de clínicas completadas')
                            ->success()
                            ->send();
                    }),

                Action::make('runSeeders')
                    ->label('Ejecutar Seeders (Reinicio Suave)')
                    ->icon('heroicon-m-play')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('¿Ejecutar Seeders?')
                    ->modalDescription('Ejecuta los seeders. Usa firstOrCreate asi que no borra datos existentes.')
                    ->modalSubmitActionLabel('Sí, ejecutarlos')
                    ->action(function () {
                        $this->lastOutput = $this->runCommand('db:seed');

                        Notification::make()
                            ->title('Seeders ejecutados')
                            ->body('Revisa la terminal para ver los detalles.')
                            ->success()
                            ->send();
                    }),
            ])->label('Migraciones y Datos')->icon('heroicon-m-circle-stack')->color('warning')->button(),

            ActionGroup::make([
                Action::make('fixDomains')
                    ->label('Reparar Dominios')
                    ->icon('heroicon-s-globe-alt')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Fix de Dominios')
                    ->modalDescription('Verifica y registra dominios faltantes.')
                    ->action(function () {
                        $out = [];
                        $out[] = '$ php artisan fix:domains';
                        $out[] = str_repeat('-', 50);

                        try {
                            $clinics = DB::table('tenants')->get();
                            $out[] = "Clínicas: {$clinics->count()}";

                            foreach ($clinics as $clinic) {
                                $out[] = "  - {$clinic->id}: {$clinic->name}";
                            }

                            $domains = DB::table('domains')->get();
                            $out[] = '';
                            $out[] = "Dominios: {$domains->count()}";

                            foreach ($domains as $domain) {
                                $out[] = "  - {$domain->domain} → {$domain->clinic_id}";
                            }

                            $targetDomain = 'clinicatest.dentalflow.digitalwebsolution.info';
                            $domainExists = DB::table('domains')
                                ->where('domain', $targetDomain)
                                ->first();

                            if ($domainExists) {
                                $out[] = '';
                                $out[] = "✅ '{$targetDomain}' ya existe";
                            } else {
                                $out[] = '';
                                $out[] = "⚠️  '{$targetDomain}' no existe";

                                $clinic = DB::table('tenants')->where('id', 'clinicatest')->first();
                                if (! $clinic) {
                                    DB::table('tenants')->insert([
                                        'id' => 'clinicatest',
                                        'name' => 'Clínica Test',
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                    $out[] = '✅ Clínica clinicatest creada';
                                }

                                DB::table('domains')->insert([
                                    'domain' => $targetDomain,
                                    'clinic_id' => 'clinicatest',
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                $out[] = '✅ Dominio registrado';
                            }

                            $out[] = str_repeat('-', 50);
                            $this->lastOutput = implode("\n", $out);

                            Notification::make()
                                ->title('Fix de dominios completado')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            $out[] = "ERROR: {$e->getMessage()}";
                            $out[] = str_repeat('-', 50);
                            $this->lastOutput = implode("\n", $out);

                            Notification::make()
                                ->title('Error en fix de dominios')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('checkInventories')
                    ->label('Verificar Inventarios')
                    ->icon('heroicon-s-clipboard-document-check')
                    ->color('info')
                    ->action(function () {
                        $out = [];
                        $out[] = '$ php artisan check:inventories';
                        $out[] = str_repeat('-', 50);

                        try {
                            $columns = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'inventories' ORDER BY ordinal_position");
                            $out[] = 'Columnas en inventories:';
                            foreach ($columns as $col) {
                                $out[] = "  - {$col->column_name} ({$col->data_type})";
                            }

                            $requiredCols = ['price', 'quantity', 'low_stock_threshold', 'items_per_unit', 'supplier'];
                            $missing = [];
                            foreach ($requiredCols as $col) {
                                if (! collect($columns)->contains('column_name', $col)) {
                                    $missing[] = $col;
                                }
                            }

                            if (! empty($missing)) {
                                $out[] = '';
                                $out[] = '⚠️  Columnas faltantes: '.implode(', ', $missing);
                                $out[] = '   Ejecuta las migraciones pendientes.';
                            } else {
                                $out[] = '';
                                $out[] = '✅ Tabla inventories correcta';
                            }

                            $inventoryCount = Inventory::count();
                            $out[] = "   Total productos: {$inventoryCount}";

                            $out[] = str_repeat('-', 50);
                            $this->lastOutput = implode("\n", $out);

                            Notification::make()
                                ->title('Verificación completada')
                                ->info()
                                ->send();
                        } catch (\Exception $e) {
                            $out[] = "ERROR: {$e->getMessage()}";
                            $out[] = str_repeat('-', 50);
                            $this->lastOutput = implode("\n", $out);

                            Notification::make()
                                ->title('Error en verificación')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])->label('Mantenimiento')->icon('heroicon-m-cog-6-tooth')->color('success')->button(),

            ActionGroup::make([
                Action::make('optimize')
                    ->label('Optimizar Sistema')
                    ->icon('heroicon-m-bolt')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('¿Optimizar Sistema?')
                    ->modalDescription('Genera caché de config, rutas y vistas.')
                    ->action(function () {
                        $this->lastOutput = $this->runCommand('optimize');

                        Notification::make()
                            ->title('Sistema optimizado')
                            ->success()
                            ->send();
                    }),

                Action::make('clearCache')
                    ->label('Limpiar Caché')
                    ->icon('heroicon-m-trash')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('¿Limpiar Caché?')
                    ->modalDescription('Limpia toda la caché del sistema.')
                    ->action(function () {
                        $this->lastOutput = $this->runCommand('optimize:clear');

                        Notification::make()
                            ->title('Caché limpiada')
                            ->success()
                            ->send();
                    }),

                Action::make('diagnostic')
                    ->label('Diagnóstico Completo')
                    ->icon('heroicon-m-magnifying-glass')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('¿Ejecutar Diagnóstico?')
                    ->modalDescription('Ejecuta diagnostic:all para revisar el estado del sistema.')
                    ->action(function () {
                        $this->lastOutput = $this->runCommand('diagnostic:all');

                        Notification::make()
                            ->title('Diagnóstico completado')
                            ->success()
                            ->send();
                    }),
            ])->label('Rendimiento')->icon('heroicon-m-bolt')->color('warning')->button(),

            Action::make('hardReset')
                ->label('HARD RESET (Peligro)')
                ->icon('heroicon-m-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('ADVERTENCIA: PÉRDIDA PERMANENTE DE DATOS')
                ->modalDescription('ESTO ELIMINARÁ TODOS LOS DATOS (migrate:fresh) Y COMENZARÁ DE CERO.')
                ->modalSubmitActionLabel('ENTIENDO, BORRAR TODOS LOS DATOS')
                ->modalIcon('heroicon-o-exclamation-triangle')
                ->hidden(! auth()->user()?->hasRole('super-admin'))
                ->action(function () {
                    if (! auth()->user()->hasRole('super-admin')) {
                        Notification::make()
                            ->title('Acceso Denegado')
                            ->body('Solo super-admin puede ejecutar Hard Reset.')
                            ->danger()
                            ->send();

                        return;
                    }

                    if (app()->environment('production')) {
                        Notification::make()
                            ->title('Operación Bloqueada')
                            ->body('Hard Reset está deshabilitado en producción.')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        Log::channel('audit')->critical('HARD RESET EXECUTED', [
                            'user_id' => auth()->id(),
                            'user_email' => auth()->user()->email,
                            'timestamp' => now()->toIso8601String(),
                            'environment' => app()->environment(),
                        ]);

                        $this->lastOutput = $this->runCommand('migrate:fresh', ['--force' => true])
                            ."\n".$this->runCommand('db:seed');

                        Log::channel('audit')->info('HARD RESET COMPLETED', [
                            'user_id' => auth()->id(),
                        ]);

                        $superAdmins = User::role('super-admin')->get();
                        foreach ($superAdmins as $admin) {
                            if ($admin->id !== auth()->id()) {
                                Mail::raw(
                                    "ALERTA - HARD RESET EJECUTADO\n\n".
                                    'Usuario: '.auth()->user()->name.' ('.auth()->user()->email.")\n".
                                    'Fecha: '.now()->toIso8601String()."\n".
                                    'IP: '.request()->ip()."\n",
                                    function ($message) use ($admin) {
                                        $message
                                            ->to($admin->email)
                                            ->subject('🚨 Reinicio Completo Ejecutado - '.config('app.name'));
                                    }
                                );
                            }
                        }

                        auth()->logout();
                        session()->invalidate();
                        session()->regenerateToken();

                        return redirect()->route('filament.admin.auth.login');
                    } catch (\Exception $e) {
                        Log::channel('audit')->error('HARD RESET FAILED', [
                            'user_id' => auth()->id(),
                            'error' => $e->getMessage(),
                        ]);

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
