<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->unique();
            $table->string('item_name');
            $table->decimal('item_price', 16, 2);
            $table->unsignedInteger('merchant_id');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('merchants')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('merchants')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('merchants')->onDelete('restrict');  
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
        Schema::dropIfExists('stores');
        Schema::enableForeignKeyConstraints();
    }
}
