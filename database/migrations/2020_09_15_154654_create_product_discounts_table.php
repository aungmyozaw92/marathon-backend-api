<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parcel_id');
            $table->unsignedInteger('merchant_id');
            $table->string('discount_type');
            $table->decimal('amount', 16, 2)->default(0);
            $table->integer('min_qty');
            $table->boolean('is_inclusive')->default(0);
            $table->boolean('is_exclusive')->default(0);
            $table->boolean('is_foc')->default(0);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->unsignedInteger('created_by_id'); 
            $table->unsignedInteger('updated_by_id')->nullable(); 
            $table->unsignedInteger('deleted_by_id')->nullable(); 
            $table->string('created_by_type'); 
            $table->string('updated_by_type')->nullable(); 
            $table->string('deleted_by_type')->nullable(); 
            $table->timestamps(); 
            $table->softDeletes(); 
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
        Schema::dropIfExists('product_discounts');
        Schema::enableForeignKeyConstraints();
    }
}
