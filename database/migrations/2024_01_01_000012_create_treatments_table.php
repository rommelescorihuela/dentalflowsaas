<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};
