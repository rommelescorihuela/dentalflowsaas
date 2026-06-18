<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('subscription_status')->nullable()->after('plan');
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_status');
            $table->index('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex(['subscription_status']);
            $table->dropColumn(['subscription_status', 'trial_ends_at']);
        });
    }
};
