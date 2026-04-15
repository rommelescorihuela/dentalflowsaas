<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CheckSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:check-schema {table=tenants}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check columns of a table, by default tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $table = $this->argument('table');
        $columns = Schema::getColumnListing($table);
        $this->info("Columns in '{$table}' table:");
        foreach ($columns as $col) {
            $this->line("- $col");
        }
    }
}
