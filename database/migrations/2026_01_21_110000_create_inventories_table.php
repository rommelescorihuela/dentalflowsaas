<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->date('expiration_date')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('low_stock_threshold')->default(10);
            $table->string('unit')->default('pieces');
            $table->integer('items_per_unit')->default(1);
            $table->string('supplier');
            $table->enum('expiration_type', ['Expirable', 'Inexpirable'])->default('Expirable');
            $table->string('category');
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
