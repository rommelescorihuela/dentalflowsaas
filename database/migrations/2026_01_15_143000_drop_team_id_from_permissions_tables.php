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
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            return;
        }

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            if (Schema::hasColumn($table->getTable(), 'team_id')) {
                // Try dropping index first if known
                //$table->dropIndex('roles_team_foreign_key_index'); // Might fail if not exists
                $table->dropColumn('team_id');
            }
        });

        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) {
            if (Schema::hasColumn($table->getTable(), 'team_id')) {
                // Primary key handling might be needed if team_id was part of it. 
                // But previous migration switched PK to clinic_id.
                $table->dropColumn('team_id');
            }
        });

        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) {
            if (Schema::hasColumn($table->getTable(), 'team_id')) {
                $table->dropColumn('team_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back team_id?
    }
};
