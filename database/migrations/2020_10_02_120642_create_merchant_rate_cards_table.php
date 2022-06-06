<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantRateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_rate_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('amount', 16, 2)->default(0);
            $table->integer('merchant_id')->nullable();
            $table->integer('merchant_associate_id')->nullable();
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
            $table->decimal('from_weight', 16, 2)->nullable();
            $table->decimal('to_weight', 16, 2)->nullable();
            $table->longText('note')->nullable();
            $table->string('platform')->nullable();
            $table->integer('min_threshold')->nullable();
            $table->string('qty_status')->nullable();
            
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
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
        Schema::dropIfExists('merchant_rate_cards');
        Schema::enableForeignKeyConstraints();
    }
}
