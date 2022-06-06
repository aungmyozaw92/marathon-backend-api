<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVariationMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variation_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id');
            $table->string('key');
            $table->string('value');
            $table->unsignedInteger('created_by_id'); 
            $table->unsignedInteger('updated_by_id')->nullable(); 
            $table->unsignedInteger('deleted_by_id')->nullable(); 
            $table->string('created_by_type'); 
            $table->string('updated_by_type')->nullable(); 
            $table->string('deleted_by_type')->nullable(); 
            $table->timestamps(); 
            $table->softDeletes(); 
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variation_metas');
    }
}
