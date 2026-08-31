<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class DeployCommand extends Command
{
    protected $signature = 'app:deploy';

    protected $description = 'Ejecuta migraciones y seeders una sola vez (para hosting sin terminal, via cron)';

    protected string $marker = 'app/deployed';

    public function handle(): int
    {
        $markerPath = storage_path($this->marker);

        if (File::exists($markerPath)) {
            $this->info('Deploy ya ejecutado anteriormente. Puedes eliminar este cron job.');
            return self::SUCCESS;
        }

        $this->info('Ejecutando migraciones...');
        Artisan::call('migrate', ['--force' => true]);
        $this->line(Artisan::output());

        $this->info('Ejecutando seeders...');
        Artisan::call('db:seed', ['--force' => true]);
        $this->line(Artisan::output());

        File::put($markerPath, now()->toDateTimeString());

        $this->info('Deploy completado. Elimina este cron job del panel.');
        return self::SUCCESS;
    }
}