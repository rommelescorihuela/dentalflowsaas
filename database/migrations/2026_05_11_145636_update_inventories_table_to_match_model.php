<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventories', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('name');
            }
            if (!Schema::hasColumn('inventories', 'quantity')) {
                $table->integer('quantity')->default(0)->after('price');
            }
            if (!Schema::hasColumn('inventories', 'low_stock_threshold')) {
                $table->integer('low_stock_threshold')->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('inventories', 'items_per_unit')) {
                $table->integer('items_per_unit')->default(1)->after('unit');
            }
            if (!Schema::hasColumn('inventories', 'supplier')) {
                $table->string('supplier')->nullable()->after('items_per_unit');
            }
            if (Schema::hasColumn('inventories', 'current_stock')) {
                $table->dropColumn('current_stock');
            }
            if (Schema::hasColumn('inventories', 'min_stock')) {
                $table->dropColumn('min_stock');
            }
            if (Schema::hasColumn('inventories', 'cost')) {
                $table->dropColumn('cost');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            if (Schema::hasColumn('inventories', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('inventories', 'quantity')) {
                $table->dropColumn('quantity');
            }
            if (Schema::hasColumn('inventories', 'low_stock_threshold')) {
                $table->dropColumn('low_stock_threshold');
            }
            if (!Schema::hasColumn('inventories', 'current_stock')) {
                $table->decimal('current_stock', 8, 2)->default(0);
            }
            if (!Schema::hasColumn('inventories', 'min_stock')) {
                $table->decimal('min_stock', 8, 2)->default(0);
            }
            if (!Schema::hasColumn('inventories', 'cost')) {
                $table->decimal('cost', 10, 2)->nullable();
            }
        });
    }
};
