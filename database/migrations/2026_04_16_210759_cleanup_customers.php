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
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('serviceaddress');
            $table->dropColumn('lot');
            $table->dropColumn('zone');
            $table->integer('adjustment')->default(0)->after('balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('serviceaddress')->nullable();
            $table->string('lot')->nullable();
            $table->string('zone')->nullable();
            $table->dropColumn('adjustment');
        });
    }
};
