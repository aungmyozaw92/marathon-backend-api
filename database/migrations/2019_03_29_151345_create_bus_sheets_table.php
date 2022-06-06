<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bus_sheets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bus_sheet_invoice')->unique()->nullable();
            $table->integer('qty');
            $table->unsignedInteger('from_bus_station_id');
            $table->unsignedInteger('delivery_id');
            $table->unsignedInteger('staff_id');
            $table->boolean('is_closed')->default(0);
            $table->boolean('is_paid')->default(0);
            $table->longText('note')->nullable()->default(null);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('from_bus_station_id')->references('id')->on('bus_stations')->onDelete('restrict');
            $table->foreign('delivery_id')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('restrict');
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
        Schema::dropIfExists('bus_sheets');
        Schema::enableForeignKeyConstraints();
    }
}
