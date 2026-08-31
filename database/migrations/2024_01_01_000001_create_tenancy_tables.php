<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('plan')->default('free');
            $table->string('subscription_status')->nullable()->after('plan');
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_status');
            $table->json('data')->nullable();
            $table->timestamps();

            $table->index('subscription_status');
        });

        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->string('clinic_id');
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
        Schema::dropIfExists('tenants');
    }
};