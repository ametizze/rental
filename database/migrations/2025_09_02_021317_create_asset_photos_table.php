<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('asset_id');
            $table->string('path', 255);       // storage path (public disk)
            $table->string('caption', 200)->nullable();
            $table->dateTime('taken_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_photos');
    }
};
