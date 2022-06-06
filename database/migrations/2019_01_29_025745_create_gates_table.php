<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            //$table->decimal('gate_rate', 16, 2);
            $table->unsignedInteger('bus_station_id');
            $table->unsignedInteger('bus_id');
            $table->boolean('gate_debit')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('bus_station_id')->references('id')->on('bus_stations')->onDelete('restrict');
           // $table->foreign('bus_id')->references('id')->on('buses')->onDelete('restrict');
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
        Schema::dropIfExists('gates');
        Schema::enableForeignKeyConstraints();
    }
}
