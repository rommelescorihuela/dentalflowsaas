<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add clinic_id column as nullable
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->string('clinic_id')->nullable()->after('role_id');
        });

        // Step 2: Update existing records to set clinic_id from the roles table
        // Use COALESCE to set '__global__' for NULL clinic_id values
        DB::statement("
            UPDATE role_has_permissions rhp
            SET clinic_id = COALESCE(r.clinic_id, '__global__')
            FROM roles r
            WHERE rhp.role_id = r.id
        ");

        // Step 3: Drop existing primary key and add new composite primary key
        Schema::table('role_has_permissions', function (Blueprint $table) {
            // Drop existing primary key
            $table->dropPrimary(['permission_id', 'role_id']);

            // Add new composite primary key including clinic_id
            $table->primary(['permission_id', 'role_id', 'clinic_id'], 'role_has_permissions_permission_role_clinic_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_has_permissions', function (Blueprint $table) {
            // Drop the composite primary key
            $table->dropPrimary('role_has_permissions_permission_role_clinic_primary');

            // Restore original primary key
            $table->primary(['permission_id', 'role_id']);

            // Drop clinic_id column
            $table->dropColumn('clinic_id');
        });
    }
};
