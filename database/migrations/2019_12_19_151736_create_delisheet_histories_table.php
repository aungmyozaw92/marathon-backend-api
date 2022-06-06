<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDelisheetHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delisheet_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('delisheet_id');
            $table->unsignedInteger('log_status_id');
            $table->longText('previous')->nullable();
            $table->longText('next')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();

            // $table->foreign('delisheet_id')->references('id')->on('pickups')->onDelete('restrict');
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
        Schema::dropIfExists('delisheet_histories');
        Schema::enableForeignKeyConstraints();
    }
}
