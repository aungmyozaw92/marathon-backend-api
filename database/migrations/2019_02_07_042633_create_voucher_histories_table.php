<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoucherHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('voucher_id');
            $table->unsignedInteger('log_status_id');
            $table->longText('previous')->nullable();
            $table->longText('next')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();

            // $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('restrict');
            // $table->foreign('log_status_id')->references('id')->on('log_statuses')->onDelete('restrict');
            // $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('voucher_histories');
        Schema::enableForeignKeyConstraints();
    }
}
