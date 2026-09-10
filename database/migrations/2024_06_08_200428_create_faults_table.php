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
        Schema::create('faults', function (Blueprint $table) {
            $table->id();
            $table->integer('site_id');
            $table->char('source')->default('not set');
            $table->char('type')->default('not set');
            $table->text('state')->nullable();
            $table->integer('alarm_level')->default(0);
            $table->timestamp('start');
            $table->timestamp('resolved')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faults');
    }
};
