<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsForHeromembership extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('zones', function (Blueprint $table) {
            $table->decimal('outsource_rate', 16, 2)->default(0);
        });
        Schema::table('staffs', function (Blueprint $table) {
            $table->unsignedInteger('hero_badge_id')->nullable()->after('points');
            $table->boolean('is_commissionable')->default(0)->after('courier_type_id');
            $table->foreign('hero_badge_id')->references('id')->on('hero_badges')->onDelete('restrict');
        });
        Schema::table('deli_sheets', function (Blueprint $table) {
            $table->unsignedInteger('courier_type_id')->nullable()->after('delivery_id');
            $table->boolean('is_commissionable')->default(0)->after('courier_type_id');
            $table->boolean('is_came_from_mobile')->default(0)->after('is_commissionable');
            $table->unsignedInteger('actby_mobile')->nullable()->after('is_came_from_mobile');
            $table->foreign('courier_type_id')->references('id')->on('courier_types')->onDelete('restrict');
            $table->foreign('actby_mobile')->references('id')->on('staffs')->onDelete('restrict');
        });
        Schema::table('pickups', function (Blueprint $table) {
            $table->boolean('is_came_from_mobile')->default(0)->after('pickuped_by_type');
            $table->unsignedInteger('actby_mobile')->nullable()->after('is_came_from_mobile');
            $table->decimal('commission_amount', 16, 2)->default(0)->after('is_came_from_mobile');
            $table->foreign('actby_mobile')->references('id')->on('staffs')->onDelete('restrict');
        });
        Schema::table('waybills', function (Blueprint $table) {
            $table->boolean('is_came_from_mobile')->default(0)->after('is_delivered');
            $table->unsignedInteger('actby_mobile')->nullable()->after('is_came_from_mobile');
            $table->decimal('commission_amount', 16, 2)->default(0)->after('is_came_from_mobile');
            $table->foreign('actby_mobile')->references('id')->on('staffs')->onDelete('restrict');
        });
        Schema::table('return_sheets', function (Blueprint $table) {
            $table->boolean('is_came_from_mobile')->default(0)->after('is_returned');
            $table->unsignedInteger('actby_mobile')->nullable()->after('is_came_from_mobile');
            $table->decimal('commission_amount', 16, 2)->default(0)->after('is_came_from_mobile');
            $table->foreign('actby_mobile')->references('id')->on('staffs')->onDelete('restrict');
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
        Schema::table('zones', function (Blueprint $table) {
            $table->dropColumn(['outsource_rate']);
        });
        Schema::table('staffs', function (Blueprint $table) {
            $table->dropForeign('staffs_hero_badge_id_foreign');
            $table->dropColumn(['hero_badge_id']);
            $table->dropColumn(['is_commissionable']);
        });
        Schema::table('deli_sheets', function (Blueprint $table) {
            $table->dropForeign('deli_sheets_courier_type_id_foreign');
            $table->dropColumn(['courier_type_id']);
            $table->dropColumn(['is_commissionable']);
            $table->dropColumn(['is_came_from_mobile']);
            $table->dropForeign('deli_sheets_actby_mobile_foreign');
            $table->dropColumn(['actby_mobile']);
        });
        Schema::table('pickups', function (Blueprint $table) {
            $table->dropColumn(['is_came_from_mobile']);
            $table->dropForeign('pickups_actby_mobile_foreign');
            $table->dropColumn(['actby_mobile']);
        });
        Schema::table('waybills', function (Blueprint $table) {
            $table->dropColumn(['is_came_from_mobile']);
            $table->dropForeign('waybills_actby_mobile_foreign');
            $table->dropColumn(['actby_mobile']);
            $table->dropColumn(['commission_amount']);
        });
        Schema::table('return_sheets', function (Blueprint $table) {
            $table->dropColumn(['is_came_from_mobile']);
            $table->dropForeign('return_sheets_actby_mobile_foreign');
            $table->dropColumn(['actby_mobile']);
            $table->dropColumn(['commission_amount']);
        });
    }
}
