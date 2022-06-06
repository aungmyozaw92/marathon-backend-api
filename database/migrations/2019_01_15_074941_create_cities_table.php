<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('name_mm')->nullable();
            //$table->decimal('delivery_rate', 16, 2);
            $table->boolean('is_collect_only')->default(0)->nullable();
            $table->boolean('is_on_demand')->default(0)->nullable();
            $table->boolean('is_available_d2d')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('locked_by')->references('id')->on('staffs')->onDelete('restrict');
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
        Schema::dropIfExists('cities');
        Schema::enableForeignKeyConstraints();
    }
}
