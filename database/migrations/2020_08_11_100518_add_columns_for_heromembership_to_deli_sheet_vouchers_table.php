<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsForHeromembershipToDeliSheetVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deli_sheet_vouchers', function (Blueprint $table) {
            $table->boolean('is_came_from_mobile')->default(0)->after('cant_deliver');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deli_sheet_vouchers', function (Blueprint $table) {
            $table->dropColumn(['is_came_from_mobile']);
        });
    }
}
