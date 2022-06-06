<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantAssociatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_associates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id');
            $table->text('label')->nullable();
            $table->longText('address');
            $table->unsignedInteger('city_id');
            $table->unsignedInteger('zone_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            //$table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('restrict');
            //$table->foreign('city_id')->references('id')->on('cities')->onDelete('restrict');
            //$table->foreign('zone_id')->references('id')->on('zones')->onDelete('restrict');
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
        Schema::dropIfExists('merchant_associates');
        Schema::enableForeignKeyConstraints();
    }
}
