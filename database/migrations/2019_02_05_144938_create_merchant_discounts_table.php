<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('merchant_discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('amount', 16, 2)->default(0);
            $table->integer('merchant_id')->nullable();
            $table->unsignedInteger('discount_type_id');
            $table->boolean('normal_or_dropoff')->default(0);
            $table->boolean('extra_or_discount')->default(0);
            $table->unsignedInteger('sender_city_id')->nullable();
            $table->unsignedInteger('receiver_city_id')->nullable();
            $table->unsignedInteger('sender_zone_id')->nullable();
            $table->unsignedInteger('receiver_zone_id')->nullable();

            $table->unsignedInteger('from_bus_station_id')->nullable();
            $table->unsignedInteger('to_bus_station_id')->nullable();

            // $table->integer('current_discount_count')->default(0);
            // $table->integer('discount_count')->default(0);
            // $table->integer('foc_count')->default(0);
            // $table->integer('current_counter')->default(0);
            // $table->integer('target_sale_count')->default(0);
            
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->longText('note')->nullable();
            $table->string('platform')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('discount_type_id')->references('id')->on('discount_types')->onDelete('restrict');
            // $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('merchant_discounts');
        Schema::enableForeignKeyConstraints();
    }
}
