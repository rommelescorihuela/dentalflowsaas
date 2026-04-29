<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('odontogram_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('total', 10, 2);
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->date('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->foreignId('budget_id')->constrained()->cascadeOnDelete();
            $table->string('treatment_name');
            $table->integer('quantity')->default(1);
            $table->decimal('cost', 10, 2);
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_items');
        Schema::dropIfExists('budgets');
    }
};
