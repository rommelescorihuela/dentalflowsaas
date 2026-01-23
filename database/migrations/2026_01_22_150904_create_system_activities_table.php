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
        Schema::create('system_activities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('clinic_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_type')->nullable(); // For separating Admin vs App Users
            $table->string('action')->index(); // create, update, login, etc.
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->text('description')->nullable();

            // Request Metadata
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('method')->nullable();
            $table->text('url')->nullable();
            $table->text('referrer')->nullable();
            $table->string('device')->nullable();
            $table->string('platform')->nullable();
            $table->string('browser')->nullable();

            // Payloads
            $table->json('payload')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_activities');
    }
};
