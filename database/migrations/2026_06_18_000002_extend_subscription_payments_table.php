<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->foreignId('subscription_id')->nullable()->after('clinic_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->date('period_start')->nullable()->after('status');
            $table->date('period_end')->nullable()->after('period_start');
            $table->string('reference')->nullable()->after('transaction_id');
            $table->string('proof_path')->nullable()->after('reference');
            $table->foreignId('verified_by')->nullable()->after('proof_path')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->index('subscription_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->dropIndex(['subscription_id']);
            $table->dropIndex(['status']);
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['subscription_id']);
            $table->dropColumn([
                'subscription_id',
                'period_start',
                'period_end',
                'reference',
                'proof_path',
                'verified_by',
                'verified_at',
            ]);
        });
    }
};
