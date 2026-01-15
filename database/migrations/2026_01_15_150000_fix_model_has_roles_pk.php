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
        $teamForeignKey = 'clinic_id';

        if (empty($tableNames)) {
            return;
        }

        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($teamForeignKey) {
            // Drop existing primary key
            $table->dropPrimary('model_has_roles_clinic_primary');

            // Add ID column and make it PK
            if (!Schema::hasColumn($table->getTable(), 'id')) {
                $table->bigIncrements('id')->first();
                // bigIncrements automatically sets primary key
            }

            // Ensure clinic_id is nullable
            $table->string($teamForeignKey)->nullable()->change();

            // Add unique index
            $table->unique([$teamForeignKey, config('permission.column_names.model_morph_key'), 'model_type', 'role_id'], 'model_has_roles_unique_identifiers');
        });

        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) use ($teamForeignKey) {
            $table->dropPrimary('model_has_permissions_clinic_primary');

            if (!Schema::hasColumn($table->getTable(), 'id')) {
                $table->bigIncrements('id')->first();
            }

            $table->string($teamForeignKey)->nullable()->change();

            $table->unique([$teamForeignKey, config('permission.column_names.model_morph_key'), 'model_type', 'permission_id'], 'model_has_permissions_unique_identifiers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert is complex, skipping for now
    }
};
