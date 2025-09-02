<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_tenants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tenant_id');
            $table->boolean('is_default')->default(false);
            $table->string('role', 20)->default('user'); // user|admin|accountant...
            $table->timestamps();

            $table->unique(['user_id', 'tenant_id']);
            $table->index(['user_id', 'tenant_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tenants');
    }
};
