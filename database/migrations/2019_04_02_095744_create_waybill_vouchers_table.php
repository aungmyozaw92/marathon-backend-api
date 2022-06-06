<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWaybillVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waybill_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('waybill_id');
            $table->unsignedInteger('voucher_id');
            $table->unsignedInteger('status')->nullable()->default(null);
            $table->longText('note')->nullable()->default(null);
            $table->unsignedInteger('priority')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('waybill_id')->references('id')->on('waybills')->onDelete('restrict');
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('waybill_vouchers');
    }
}
