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
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('role_has_permissions', function (Blueprint $table) {
            // Drop the composite primary key that includes clinic_id
            $table->dropPrimary('role_has_permissions_permission_role_clinic_primary');

            // Drop the clinic_id column
            $table->dropColumn('clinic_id');

            // Restore the standard primary key
            $table->primary(['permission_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_has_permissions', function (Blueprint $table) {
            // Drop the standard primary key
            $table->dropPrimary(['permission_id', 'role_id']);

            // Add the clinic_id column back
            $table->string('clinic_id')->nullable();

            // Note: We cannot easily restore the data for clinic_id here without complex logic
            // so we just restore the schema.

            // Restore the composite primary key
            $table->primary(['permission_id', 'role_id', 'clinic_id'], 'role_has_permissions_permission_role_clinic_primary');
        });
    }
};
