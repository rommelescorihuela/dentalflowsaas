<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_banners', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id');
            $table->foreign('clinic_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('type')->default('info');
            $table->string('color')->default('blue');
            $table->string('icon')->nullable();
            $table->string('link')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'is_active']);
        });

        Schema::create('clinic_settings', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_id')->unique();
            $table->foreign('clinic_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->string('primary_color')->default('#06b6d4');
            $table->string('secondary_color')->default('#0891b2');
            $table->string('accent_color')->default('#0e7490');
            $table->boolean('dark_mode')->default(false);
            $table->string('landing_title')->nullable();
            $table->text('landing_description')->nullable();
            $table->string('landing_logo')->nullable();
            $table->string('landing_hero_image')->nullable();
            $table->text('landing_services')->nullable();
            $table->string('landing_phone')->nullable();
            $table->string('landing_email')->nullable();
            $table->string('landing_address')->nullable();
            $table->string('landing_facebook')->nullable();
            $table->string('landing_instagram')->nullable();
            $table->string('landing_whatsapp')->nullable();
            $table->boolean('landing_enabled')->default(false);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('appointment_reminders')->default(true);
            $table->integer('reminder_hours_before')->default(24);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_settings');
        Schema::dropIfExists('dashboard_banners');
    }
};