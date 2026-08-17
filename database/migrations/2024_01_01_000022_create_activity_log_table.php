<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->string('subject_type')->nullable();
            $table->string('subject_id')->nullable();
            $table->index(['subject_type', 'subject_id'], 'activity_log_subject_index');
            $table->string('causer_type')->nullable();
            $table->string('causer_id')->nullable();
            $table->index(['causer_type', 'causer_id'], 'activity_log_causer_index');
            $table->json('properties')->nullable();
            $table->string('event')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->string('clinic_id')->nullable()->index();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('method')->nullable();
            $table->text('url')->nullable();
            $table->text('referrer')->nullable();
            $table->timestamps();

            $table->index('log_name');
            $table->index('created_at');
        });

        if (Schema::hasTable('system_activities')) {
            // Migrate existing audit data into the spatie-compatible shape.
            DB::statement(<<<'SQL'
                INSERT INTO activity_log (
                    log_name, description, subject_type, subject_id,
                    causer_type, causer_id, properties, event, batch_uuid,
                    clinic_id, ip_address, user_agent, method, url, referrer,
                    created_at, updated_at
                )
                SELECT
                    'default',
                    description,
                    subject_type,
                    subject_id,
                    CASE WHEN user_type IS NULL THEN NULL ELSE 'App\Models\' || user_type END,
                    user_id,
                    jsonb_strip_nulls(jsonb_build_object(
                        'old', old_values,
                        'attributes', new_values,
                        'payload', payload
                    )),
                    CASE action
                        WHEN 'create' THEN 'created'
                        WHEN 'update' THEN 'updated'
                        WHEN 'delete' THEN 'deleted'
                        ELSE action
                    END,
                    NULL,
                    clinic_id,
                    ip_address,
                    user_agent,
                    method,
                    url,
                    referrer,
                    created_at,
                    updated_at
                FROM system_activities
            SQL);

            Schema::dropIfExists('system_activities');
        }
    }

    public function down(): void
    {
        Schema::create('system_activities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('clinic_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_type')->nullable();
            $table->string('action')->index();
            $table->string('subject_type')->nullable();
            $table->string('subject_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('method')->nullable();
            $table->text('url')->nullable();
            $table->text('referrer')->nullable();
            $table->string('device')->nullable();
            $table->string('platform')->nullable();
            $table->string('browser')->nullable();
            $table->json('payload')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index('created_at');
        });

        DB::statement(<<<'SQL'
            INSERT INTO system_activities (
                uuid, clinic_id, user_id, user_type, action,
                subject_type, subject_id, description,
                ip_address, user_agent, method, url, referrer,
                payload, old_values, new_values,
                created_at, updated_at
            )
            SELECT
                lower(gen_random_uuid()::text),
                clinic_id,
                causer_id,
                CASE WHEN causer_type IS NULL THEN NULL ELSE REPLACE(causer_type, 'App\Models\', '') END,
                CASE event
                    WHEN 'created' THEN 'create'
                    WHEN 'updated' THEN 'update'
                    WHEN 'deleted' THEN 'delete'
                    ELSE COALESCE(event, 'update')
                END,
                subject_type,
                subject_id::text,
                description,
                ip_address,
                user_agent,
                method,
                url,
                referrer,
                (properties::jsonb ->> 'payload')::json,
                properties::jsonb -> 'old',
                properties::jsonb -> 'attributes',
                created_at,
                updated_at
            FROM activity_log
        SQL);

        Schema::dropIfExists('activity_log');
    }
};
