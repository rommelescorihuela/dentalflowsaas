<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odontograms', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('date');
            $table->string('status')->default('in_progress');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
            $table->index(['clinic_id', 'patient_id']);
        });

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

        Schema::create('tooth_images', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->foreign('clinic_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreignId('odontogram_id')->constrained('odontograms')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->unsignedTinyInteger('tooth_number');
            $table->string('image_type')->default('clinical');
            $table->string('file_path');
            $table->string('file_name');
            $table->text('description')->nullable();
            $table->date('image_date');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['clinic_id', 'patient_id', 'tooth_number']);
        });

        Schema::create('tooth_notes', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->foreign('clinic_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreignId('odontogram_id')->constrained('odontograms')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->unsignedTinyInteger('tooth_number');
            $table->string('note_type')->default('observation');
            $table->text('content');
            $table->date('note_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['clinic_id', 'patient_id', 'tooth_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tooth_notes');
        Schema::dropIfExists('tooth_images');
        Schema::dropIfExists('clinical_records');
        Schema::dropIfExists('odontograms');
    }
};