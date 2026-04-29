<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procedure_prices', function (Blueprint $table) {
            $table->string('diagnosis_code')->nullable()->after('procedure_name');
            $table->index('diagnosis_code');
        });
    }

    public function down(): void
    {
        Schema::table('procedure_prices', function (Blueprint $table) {
            $table->dropIndex(['diagnosis_code']);
            $table->dropColumn('diagnosis_code');
        });
    }
};
