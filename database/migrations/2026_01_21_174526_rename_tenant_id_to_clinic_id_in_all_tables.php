<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'odontograms',
            'procedure_prices',
            'inventories',
            'users',
            'domains',
            'patients',
            'appointments',
            'treatments',
            'clinical_records',
            'budgets',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->renameColumn('clinic_id', 'clinic_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'odontograms',
            'procedure_prices',
            'inventories',
            'users',
            'domains',
            'patients',
            'appointments',
            'treatments',
            'clinical_records',
            'budgets',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->renameColumn('clinic_id', 'clinic_id');
            });
        }
    }
};
