<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clinical_records', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('odontogram_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('procedure_price_id')->nullable()->constrained('procedure_prices')->nullOnDelete();
            $table->integer('tooth_number');
            $table->string('surface')->nullable();
            $table->string('diagnosis_code')->nullable();
            $table->string('treatment_status')->default('planned');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
            $table->index(['clinic_id', 'patient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_records');
    }
};
