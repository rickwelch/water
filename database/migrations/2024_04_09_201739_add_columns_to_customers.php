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
            $table->integer('balance')->after('stripe_customer_id')->default(0);
            $table->boolean('e_billing')->after('stripe_customer_id')->default(false);
            $table->boolean('yearly_billing')->after('e_billing')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('balance');
            $table->dropColumn('e_billing');
            $table->dropColumn('yearly_billing');
        });
    }
};
