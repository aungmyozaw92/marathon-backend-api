<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliSheetVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deli_sheet_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('delisheet_id');
            $table->unsignedInteger('voucher_id');
            $table->unsignedInteger('ATC_receiver')->nullable();
            $table->longText('note')->nullable()->default(null);
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('delivery_status')->default(0);
            $table->boolean('return')->default(0);
            $table->boolean('cant_deliver')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('delisheet_id')->references('id')->on('deli_sheets')->onDelete('restrict');
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('restrict');
            $table->foreign('ATC_receiver')->references('id')->on('staffs')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deli_sheet_vouchers');
    }
}
