<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->string('name');
            $table->string('category')->nullable();
            $table->decimal('current_stock', 8, 2)->default(0);
            $table->decimal('min_stock', 8, 2)->default(0);
            $table->string('unit')->default('unit');
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
