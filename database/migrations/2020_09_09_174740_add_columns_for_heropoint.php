<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsForHeropoint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staffs', function (Blueprint $table) {
            $table->boolean('is_pointable')->default(0)->after('is_commissionable');
        });
        Schema::table('deli_sheets', function (Blueprint $table) {
            $table->boolean('is_pointable')->default(0)->after('is_commissionable');
        });
        Schema::table('pickups', function (Blueprint $table) {
            $table->unsignedInteger('courier_type_id')->nullable()->after('commission_amount');
            $table->boolean('is_commissionable')->default(0)->after('courier_type_id');
            $table->boolean('is_pointable')->default(0)->after('is_commissionable');
            $table->foreign('courier_type_id')->references('id')->on('courier_types')->onDelete('restrict');
        });
        Schema::table('return_sheets', function (Blueprint $table) {
            $table->unsignedInteger('courier_type_id')->nullable()->after('commission_amount');
            $table->boolean('is_commissionable')->default(0)->after('courier_type_id');
            $table->boolean('is_pointable')->default(0)->after('is_commissionable');
            $table->foreign('courier_type_id')->references('id')->on('courier_types')->onDelete('restrict');
        });
        Schema::table('waybills', function (Blueprint $table) {
            $table->unsignedInteger('courier_type_id')->nullable()->after('commission_amount');
            $table->boolean('is_commissionable')->default(0)->after('courier_type_id');
            $table->boolean('is_pointable')->default(0)->after('is_commissionable');
            $table->foreign('courier_type_id')->references('id')->on('courier_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staffs', function (Blueprint $table) {
            $table->dropColumn(['is_pointable']);
        });
        Schema::table('deli_sheets', function (Blueprint $table) {
            $table->dropColumn(['is_pointable']);
        });
        Schema::table('pickups', function (Blueprint $table) {
            $table->dropForeign('pickups_courier_type_id_foreign');
            $table->dropColumn(['courier_type_id']);
            $table->dropColumn(['is_commissionable']);
            $table->dropColumn(['is_pointable']);
        });
        Schema::table('return_sheets', function (Blueprint $table) {
            $table->dropForeign('return_sheets_courier_type_id_foreign');
            $table->dropColumn(['courier_type_id']);
            $table->dropColumn(['is_commissionable']);
            $table->dropColumn(['is_pointable']);
        });
        Schema::table('waybills', function (Blueprint $table) {
            $table->dropForeign('waybills_courier_type_id_foreign');
            $table->dropColumn(['courier_type_id']);
            $table->dropColumn(['is_commissionable']);
            $table->dropColumn(['is_pointable']);
        });
    }
}
