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
            $table->dropColumn('payment_amount');
            $table->dropColumn('payment_date');
            $table->dropColumn('deposit_date');
            $table->dropColumn('payment_type');
            $table->dropColumn('check_number');
            $table->string('invoice_type')->after('id')->default('usage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('payment_amount')->nullable();
            $table->date('payment_date')->nullable();
            $table->date('deposit_date')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('check_number')->nullable();
            $table->dropColumn('invoice_type');
        });
    }
};
