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
        Schema::create('service_sites', function (Blueprint $table) {
            $table->id();
            $table->char('address', 255);
            $table->char('lot', 255)->nullable();
            $table->char('zone', 255)->default('4');
            $table->boolean('connected')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_sites');
    }
};
