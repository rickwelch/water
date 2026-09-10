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
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('due_date')->after('balance')->nullable();
            $table->string('check_number')->after('payment_amount')->nullable();
            $table->date('deposit_date')->after('payment_date')->nullable();
            $table->string('notes')->after('deposit_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('due_date');
            $table->dropColumn('check_number');
            $table->dropColumn('deposit_date');
            $table->dropColumn('notes');
        });
    }
};
