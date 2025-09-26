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
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity'); // Current stock level
            $table->string('unit')->comment('e.g., box, liter, unit');
            $table->string('reference_code')->unique()->nullable(); // Barcode/SKU/Serial
            $table->string('photo_path')->nullable(); // Path to photo

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
