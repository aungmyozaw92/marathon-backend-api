<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id'); 
            $table->unsignedInteger('tag_id'); 
            $table->unsignedInteger('created_by_id'); 
            $table->unsignedInteger('updated_by_id')->nullable(); 
            $table->unsignedInteger('deleted_by_id')->nullable(); 
            $table->string('created_by_type'); 
            $table->string('updated_by_type')->nullable(); 
            $table->string('deleted_by_type')->nullable(); 
            $table->timestamps(); 
            $table->softDeletes(); 
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('restrict');
        
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
        Schema::dropIfExists('product_tags');
        Schema::enableForeignKeyConstraints();
    }
}
