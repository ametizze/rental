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
        Schema::table('invoices', function (Blueprint $table) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'rental_id')) {
                    // Column 'rental_id' is nullable, as invoices can be created manually.
                    $table->foreignId('rental_id')->nullable()->after('customer_id')->constrained()->onDelete('set null');
                }
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'rental_id')) {
                $table->dropForeign(['rental_id']);
                $table->dropColumn('rental_id');
            }
        });
    }
};
