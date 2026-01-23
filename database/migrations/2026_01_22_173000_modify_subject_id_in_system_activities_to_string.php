<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('system_activities', function (Blueprint $table) {
            // Change subject_id to string to support non-integer IDs (like Clinic subdomains)
            $table->string('subject_id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('system_activities', function (Blueprint $table) {
            // Revert is risky if data exists, but strict reverse would be unsignedBigInteger
            // We'll leave it as is or try to revert if empty.
            // $table->unsignedBigInteger('subject_id')->change(); 
        });
    }
};
