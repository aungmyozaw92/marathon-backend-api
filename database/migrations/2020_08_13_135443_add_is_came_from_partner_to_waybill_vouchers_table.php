<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsCameFromPartnerToWaybillVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('waybill_vouchers', function (Blueprint $table) {
            $table->boolean('is_came_from_partner')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('waybill_vouchers', function (Blueprint $table) {
            $table->dropColumn(['is_came_from_partner']);
        });
    }
}
