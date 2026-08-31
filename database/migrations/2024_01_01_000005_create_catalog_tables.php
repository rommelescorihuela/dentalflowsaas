<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedure_prices', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->string('procedure_name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('duration')->nullable();
            $table->string('diagnosis_code')->nullable();
            $table->string('category')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index('diagnosis_code');
        });

        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->string('name');
            $table->string('category')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('low_stock_threshold')->default(0);
            $table->string('unit')->default('unit');
            $table->integer('items_per_unit')->default(1);
            $table->string('supplier')->nullable();
            $table->string('expiration_type')->default('Inexpirable');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index('clinic_id', 'idx_inventories_clinic_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('procedure_prices');
    }
};