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
        Schema::table('transactions', function (Blueprint $table) {
            // New Foreign Keys for reporting
            $table->foreignId('category_id')->nullable()->after('type')->constrained('transaction_categories')->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->after('source_type')->constrained()->onDelete('set null');

            // New Status and Date Fields for receivables (income)
            $table->date('due_date')->nullable()->after('date');
            $table->string('status')->default('pending')->after('due_date')->comment('pending, received, overdue, scheduled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['category_id', 'customer_id', 'due_date', 'status']);
        });
    }
};
