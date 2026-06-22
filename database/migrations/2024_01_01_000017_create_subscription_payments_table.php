<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->foreignId('subscription_id')->nullable()->after('clinic_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->string('method');
            $table->string('status')->default('pending');
            $table->date('period_start')->nullable()->after('status');
            $table->date('period_end')->nullable()->after('period_start');
            $table->string('transaction_id')->nullable();
            $table->string('reference')->nullable()->after('transaction_id');
            $table->string('proof_path')->nullable()->after('reference');
            $table->foreignId('verified_by')->nullable()->after('proof_path')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
            $table->index('subscription_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
