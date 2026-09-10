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
        Schema::create('dailies', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->char('site', 32);
            $table->integer('mainpumptime')->nullable();
            $table->integer('wellpumptime')->nullable();
            $table->integer('mainpumpcurrent')->nullable();
            $table->integer('wellpumpcurrent')->nullable();
            $table->float('level', 7, 2)->nullable();
            $table->float('temperature', 7, 2)->nullable();
            $table->float('pressure', 7, 2)->nullable();
            $table->float('humidity', 7, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dailies');
    }
};
