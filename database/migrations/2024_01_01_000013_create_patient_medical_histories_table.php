<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_medical_histories', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();

            $table->text('antecedentes_personales')->nullable();
            $table->text('antecedentes_familiares')->nullable();
            $table->text('alergias')->nullable();
            $table->text('medicamentos_actuales')->nullable();
            $table->text('enfermedades_cronicas')->nullable();

            $table->text('habitos')->nullable();
            $table->text('cirugias_previas')->nullable();

            $table->text('historia_dental')->nullable();

            $table->string('presion_arterial')->nullable();
            $table->string('frecuencia_cardiaca')->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('altura', 5, 2)->nullable();
            $table->string('grupo_sanguineo')->nullable();

            $table->text('firma_paciente')->nullable();
            $table->timestamp('fecha_firma')->nullable();
            $table->string('nombre_testigo')->nullable();
            $table->string('rut_testigo')->nullable();

            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
            $table->unique('patient_id');
            $table->index(['clinic_id', 'patient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_medical_histories');
    }
};
