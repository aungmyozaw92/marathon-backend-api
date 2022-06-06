<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackingStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status')->unique();
            $table->string('status_en')->unique();
            $table->string('status_mm')->unique();
            $table->string('description')->nullable()->default(null);
            $table->string('description_mm')->nullable()->default(null);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
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
        Schema::dropIfExists('tracking_statuses');
        Schema::enableForeignKeyConstraints();
    }
}
