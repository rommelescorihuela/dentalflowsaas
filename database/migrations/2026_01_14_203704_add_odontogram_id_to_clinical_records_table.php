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
        Schema::table('clinical_records', function (Blueprint $table) {
            $table->foreignId('odontogram_id')->nullable()->after('patient_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clinical_records', function (Blueprint $table) {
            $table->dropForeign(['odontogram_id']);
            $table->dropColumn('odontogram_id');
        });
    }
};
