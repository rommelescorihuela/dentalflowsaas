<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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

            $table->index('status', 'idx_budgets_status');
            $table->index(['patient_id', 'status'], 'idx_budgets_patient_status');
        });

        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->foreignId('budget_id')->constrained()->cascadeOnDelete();
            $table->foreignId('procedure_price_id')->nullable()->constrained('procedure_prices')->nullOnDelete();
            $table->string('treatment_name');
            $table->integer('quantity')->default(1);
            $table->decimal('cost', 10, 2);
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('budget_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('method');
            $table->string('status')->default('pending');
            $table->string('reference_id')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');

            $table->index('paid_at', 'idx_payments_paid_at');
            $table->index('status', 'idx_payments_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('budget_items');
        Schema::dropIfExists('budgets');
    }
};