<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('procedure_prices', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->string('procedure_name');
            $table->string('diagnosis_code')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('duration');
            $table->string('image_path')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
            $table->index('diagnosis_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedure_prices');
    }
};
