<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->index('start_time', 'idx_appointments_start_time');
            $table->index('end_time', 'idx_appointments_end_time');
            $table->index(['patient_id', 'status'], 'idx_appointments_patient_status');
            $table->index('user_id', 'idx_appointments_user_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('paid_at', 'idx_payments_paid_at');
            $table->index('status', 'idx_payments_status');
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->index('status', 'idx_budgets_status');
            $table->index(['patient_id', 'status'], 'idx_budgets_patient_status');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->index('clinic_id', 'idx_inventories_clinic_id');
        });

        Schema::table('procedure_inventory', function (Blueprint $table) {
            $table->index(['procedure_price_id', 'inventory_id'], 'idx_procedure_inventory_composite');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('idx_appointments_start_time');
            $table->dropIndex('idx_appointments_end_time');
            $table->dropIndex('idx_appointments_patient_status');
            $table->dropIndex('idx_appointments_user_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_paid_at');
            $table->dropIndex('idx_payments_status');
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->dropIndex('idx_budgets_status');
            $table->dropIndex('idx_budgets_patient_status');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex('idx_inventories_clinic_id');
        });

        Schema::table('procedure_inventory', function (Blueprint $table) {
            $table->dropIndex('idx_procedure_inventory_composite');
        });
    }
};
