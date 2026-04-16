<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;

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

                        // 2. Check if permissions table exists but migration is not registered
                        $hasPermissionsTable = \Illuminate\Support\Facades\Schema::hasTable('permissions');
                        $isPermissionMigrated = \Illuminate\Support\Facades\DB::table('migrations')
                            ->where('migration', '2026_01_15_130000_create_permission_tables')
                            ->exists();

                        if ($hasPermissionsTable && !$isPermissionMigrated) {
                            $maxBatch = \Illuminate\Support\Facades\DB::table('migrations')->max('batch') ?? 0;
                            \Illuminate\Support\Facades\DB::table('migrations')->insert([
                                'migration' => '2026_01_15_130000_create_permission_tables',
                                'batch' => $maxBatch + 1,
                            ]);
                            $messages[] = 'Permissions migration marked as completed (table already existed).';
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
        ];
    }
}
