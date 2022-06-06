<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no')->nullable();
            $table->unsignedInteger('merchant_id');
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('receiver_address');
            $table->string('receiver_email')->nullable();
            $table->unsignedInteger('sender_city_id');
            $table->unsignedInteger('sender_zone_id');
            $table->unsignedInteger('receiver_city_id');
            $table->unsignedInteger('receiver_zone_id');
            $table->unsignedInteger('payment_type_id');
            $table->unsignedInteger('global_scale_id');
            $table->string('remark')->nullable();
            $table->string('thirdparty_invoice')->nullable();
            $table->decimal('total_weight', 16, 3)->default(0);
            $table->decimal('total_qty', 16, 2)->default(0);
            $table->decimal('total_price', 16, 2)->default(0);
            $table->decimal('total_delivery_amount', 16, 2)->default(0);
            $table->boolean('status')->default(false);
            $table->string('platform')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
