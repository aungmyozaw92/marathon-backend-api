<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourierTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courier_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->decimal('rate', 16, 2);
            $table->decimal('cbm', 16, 2);
            $table->decimal('weight', 16, 2);
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
        Schema::dropIfExists('courier_types');
        Schema::enableForeignKeyConstraints();
    }
}
