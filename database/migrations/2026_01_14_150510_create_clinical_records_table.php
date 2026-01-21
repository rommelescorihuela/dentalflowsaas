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
            $table->integer('tooth_number');
            $table->string('surface')->nullable(); // Distal, Mesial, Occlusal, etc.
            $table->string('diagnosis_code')->nullable(); // Caries, Extraction, etc.
            $table->string('treatment_status')->default('planned'); // planned, in_progress, completed
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_records');
    }
};
