<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Billing;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->date('period_end')->after('service_date')->nullable();
            $table->date('period_start')->after('service_date')->nullable();
            $table->string('footer_note')->after('billing_amount')->nullable();
        });
        $billings = Billing::all();
        foreach ($billings as $billing) {
            $gs = explode('-', $billing->service_date);
            $billing->period_start = date('Y-m-d',strtotime($gs[0]));
            $billing->period_end = date('Y-m-d',strtotime($gs[1]));
            $billing->save();
        }
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn('service_date');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->string('service_date')->after('billing_date');
        });
        $billings = Billing::all();
        foreach ($billings as $billing) {
            $billing->service_date = date('m/d/Y',strtotime($billing->period_start)) . ' - ' . date('m/d/Y',strtotime($billing->period_end));
            $billing->save();
        }
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn('period_end');
            $table->dropColumn('period_start');
            $table->dropColumn('footer_note');
        });
    }
};
