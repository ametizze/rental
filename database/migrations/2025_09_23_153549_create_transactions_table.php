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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('expense')->comment('income, expense');
            $table->decimal('amount', 10, 2);
            $table->string('description');
            $table->foreignId('source_id')->nullable()->comment('ID of the related record, e.g., invoice_id');
            $table->string('source_type')->nullable()->comment('Class of the related record, e.g., App\\Models\\Invoice');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
