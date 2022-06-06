<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCityIdToTrackingVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_vouchers', function (Blueprint $table) {
            $table->unsignedInteger('city_id')->nullable()->after('voucher_id');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_vouchers', function (Blueprint $table) {
            $table->dropForeign('tracking_vouchers_city_id_foreign');
            $table->dropColumn(['city_id']);
        });
    }
}
