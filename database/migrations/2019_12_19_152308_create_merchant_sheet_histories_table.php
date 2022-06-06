<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantSheetHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_sheet_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_sheet_id');
            $table->unsignedInteger('log_status_id');
            $table->longText('previous')->nullable();
            $table->longText('next')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();

            // $table->foreign('merchant_sheet_id')->references('id')->on('pickups')->onDelete('restrict');
            $table->foreign('log_status_id')->references('id')->on('log_statuses')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
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
        Schema::dropIfExists('merchant_sheet_histories');
        Schema::enableForeignKeyConstraints();
    }
}
