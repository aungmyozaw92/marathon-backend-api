<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateColumnsForSheets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('pickups', function (Blueprint $table) {
            $table->timestamp('closed_date')->nullable()->after('is_closed');
        });
        Schema::table('deli_sheets', function (Blueprint $table) {
            $table->timestamp('closed_date')->nullable()->after('is_closed');
        });
        Schema::table('deli_sheet_vouchers', function (Blueprint $table) {
            $table->timestamp('finished_date')->nullable()->after('cant_deliver');
        });
        Schema::table('waybills', function (Blueprint $table) {
            $table->timestamp('confirmed_date')->nullable()->after('is_confirm');
            $table->timestamp('delivered_date')->nullable()->after('is_delivered');
            $table->timestamp('closed_date')->nullable()->after('is_closed');
        });
        Schema::table('return_sheets', function (Blueprint $table) {
            $table->timestamp('returned_date')->nullable()->after('is_return');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('pickups', function (Blueprint $table) {
            $table->dropColumn(['closed_date']);
        });
        Schema::table('deli_sheets', function (Blueprint $table) {
            $table->dropColumn(['closed_date']);
        });
        Schema::table('deli_sheet_vouchers', function (Blueprint $table) {
            $table->dropColumn(['finished_date']);
        });
        Schema::table('waybills', function (Blueprint $table) {
            $table->dropColumn(['confirmed_date', 'delivered_date', 'closed_date']);
        });
        Schema::table('return_sheets', function (Blueprint $table) {
            $table->dropColumn(['returned_date']);
        });
    }
}
