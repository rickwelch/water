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
        Schema::create('cellphones', function (Blueprint $table) {
            $table->id();
            $table->string('number', 16);
            $table->string('status')->default('valid');
            $table->string('owner')->nullable();
            $table->unsignedInteger('customer_id')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index('number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cellphones');
    }
};
