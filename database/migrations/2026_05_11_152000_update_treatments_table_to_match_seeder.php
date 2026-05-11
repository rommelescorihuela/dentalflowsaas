<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            if (Schema::hasColumn('treatments', 'description') && !Schema::hasColumn('treatments', 'name')) {
                $table->renameColumn('description', 'name');
            }
            
            if (!Schema::hasColumn('treatments', 'price')) {
                $table->decimal('price', 12, 2)->default(0)->after('name');
            }
            
            if (!Schema::hasColumn('treatments', 'code')) {
                $table->string('code')->nullable()->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            if (Schema::hasColumn('treatments', 'name') && !Schema::hasColumn('treatments', 'description')) {
                $table->renameColumn('name', 'description');
            }
            
            if (Schema::hasColumn('treatments', 'price')) {
                $table->dropColumn('price');
            }
            
            if (Schema::hasColumn('treatments', 'code')) {
                $table->dropColumn('code');
            }
        });
    }
};
