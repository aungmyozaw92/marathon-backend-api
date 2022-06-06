<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysConstraintsToAllTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            // $table->foreign('locked_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('zones', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('pickups', function (Blueprint $table) {
            // $table->foreign('opened_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('created_by_id')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            // $table->foreign('created_by_id')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('tracking_statuses', function (Blueprint $table) {
            // $table->foreign('created_by_id')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });


        Schema::table('voucher_associates', function (Blueprint $table) {
            // $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('courier_types', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('staffs', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('call_statuses', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('delivery_statuses', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('store_statuses', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('metas', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('bus_stations', function (BluePrint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('gates', function (BluePrint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('customers', function (BluePrint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('flags', function (BluePrint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('badges', function (BluePrint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('merchant_discounts', function (BluePrint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('deli_sheets', function (BluePrint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('waybills', function (BluePrint $table) {
            // $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('bus_sheets', function (BluePrint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('return_sheets', function (BluePrint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('deli_sheet_vouchers', function (BluePrint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('waybill_vouchers', function (BluePrint $table) {
            // $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('bus_sheet_vouchers', function (BluePrint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });

        Schema::table('return_sheet_vouchers', function (BluePrint $table) {
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
        });
    }
}
