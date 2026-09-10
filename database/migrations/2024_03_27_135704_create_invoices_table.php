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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->char('invoice_number', 32);
            $table->char('stripe_invoice_id', 32)->nullable();
            $table->char('stripe_customer_id', 32)->nullable();
            $table->char('status', 32)->default('open');
            $table->integer('customer_qid');
            $table->integer('balance')->nullable();
            $table->integer('payment_amount')->nullable();
            $table->date('payment_date')->nullable();
            $table->char('payment_type', 32)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
