<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWaybillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waybills', function (Blueprint $table) {
            $table->increments('id');
            $table->string('waybill_invoice')->unique()->nullable();
            $table->unsignedInteger('qty')->default(0);
            // $table->unsignedInteger('voucher_id');
            $table->unsignedInteger('from_bus_station_id');
            $table->unsignedInteger('to_bus_station_id');
            $table->unsignedInteger('gate_id');
            $table->unsignedInteger('from_city_id');
            $table->unsignedInteger('to_city_id');
            $table->unsignedInteger('delivery_id');
            $table->unsignedInteger('staff_id');
            $table->longText('note')->nullable();
            $table->decimal('actual_bus_fee', 16, 2)->nullable()->default(null);
            // $table->unsignedInteger('delivery_status_id');
            $table->boolean('is_closed')->default(0);
            $table->boolean('is_paid')->default(0);
            $table->boolean('is_received')->default(0);
            $table->boolean('is_delivered')->default(0);
            $table->boolean('is_scanned')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
 
            // $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('restrict');
            $table->foreign('delivery_id')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('restrict');
            $table->foreign('from_bus_station_id')->references('id')->on('bus_stations')->onDelete('restrict');
            $table->foreign('to_bus_station_id')->references('id')->on('bus_stations')->onDelete('restrict');
            $table->foreign('from_city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->foreign('to_city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->foreign('gate_id')->references('id')->on('gates')->onDelete('restrict');
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
        Schema::dropIfExists('waybills');
        Schema::enableForeignKeyConstraints();
    }
}
