<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('procedure_price_id')->nullable()->constrained('procedure_prices')->nullOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('status')->default('scheduled');
            $table->string('type')->default('control');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');

            $table->index('start_time', 'idx_appointments_start_time');
            $table->index('end_time', 'idx_appointments_end_time');
            $table->index(['patient_id', 'status'], 'idx_appointments_patient_status');
            $table->index('user_id', 'idx_appointments_user_id');
        });

        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 12, 2)->default(0);
            $table->string('code')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatments');
        Schema::dropIfExists('appointments');
    }
};