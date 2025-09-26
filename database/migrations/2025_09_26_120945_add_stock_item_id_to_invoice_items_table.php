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
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'stock_item_id')) {
                // Coluna 'equipment_id' já existia. Adicionamos a de estoque.
                $table->foreignId('stock_item_id')->nullable()->constrained('stock_items')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_items', 'stock_item_id')) {
                $table->dropForeign(['stock_item_id']);
                $table->dropColumn('stock_item_id');
            }
        });
    }
};
