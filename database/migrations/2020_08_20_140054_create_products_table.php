<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->unique();
            $table->unsignedInteger('merchant_id');
            $table->string('sku')->unique()->nullable();
            $table->string('item_name');
            $table->decimal('item_price', 16, 2);
            $table->boolean('is_seasonal')->default(0);
            $table->boolean('is_feature')->default(0);
            $table->decimal('weight', 16, 2)->default(2);
            $table->decimal('lwh', 16, 2)->default(20);
            $table->unsignedInteger('product_type_id')->nullable();
            $table->unsignedInteger('created_by_id')->nullable();
            $table->string('created_by_type')->nullable();
            $table->unsignedInteger('updated_by_id')->nullable();
            $table->string('updated_by_type')->nullable();
            $table->unsignedInteger('deleted_by_id')->nullable();
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('products');
        Schema::enableForeignKeyConstraints();
    }
}
