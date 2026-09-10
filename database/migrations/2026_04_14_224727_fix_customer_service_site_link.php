<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ServiceSite;
use App\Models\Customer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_sites', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->after('meta')->nullable();
        });
        $sites = ServiceSite::where('connected', 1)->get();
        foreach ($sites as $site) {
            $site->customer_id = Customer::where('service_site_id', $site->id)->first()->id;
            $site->save();
        }
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['service_site_id']);
            $table->dropColumn('service_site_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('service_site_id')->after('qid')->nullable();
            $table->foreign('service_site_id')->references('id')->on('service_sites');
        });

        $customers = Customer::where('status', 1)->get();
        foreach($customers as $customer){
            $customer->service_site_id = ServiceSite::where('customer_id', $customer->id)->first()->id;
            $customer->save();
        }

        Schema::table('service_sites', function (Blueprint $table) {
            $table->dropColumn('customer_id');
        });
    }
};
