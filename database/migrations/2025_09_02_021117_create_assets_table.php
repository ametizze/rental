<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('code', 30);                 // human short code
            $table->string('make', 120)->nullable();    // brand
            $table->string('model', 120)->nullable();   // model name
            $table->string('serial_number', 120)->nullable();
            $table->smallInteger('year')->nullable();
            $table->string('status', 20)->default('available'); // available|rented|maintenance
            $table->integer('price_per_day_cents')->nullable(); // optional default daily rate
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
