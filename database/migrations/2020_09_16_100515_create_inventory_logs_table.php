<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inventory_id');
            $table->integer('qty');
            $table->unsignedInteger('created_by_id'); 
            $table->unsignedInteger('updated_by_id')->nullable(); 
            $table->unsignedInteger('deleted_by_id')->nullable(); 
            $table->string('created_by_type'); 
            $table->string('updated_by_type')->nullable(); 
            $table->string('deleted_by_type')->nullable(); 
            $table->timestamps(); 
            $table->softDeletes(); 
            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_logs');
    }
}
