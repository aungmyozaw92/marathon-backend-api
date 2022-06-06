<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('receiver_id');
            $table->unsignedInteger('pickup_id')->nullable();
            $table->unsignedInteger('qr_associate_id')->nullable();
            $table->string('voucher_invoice')->unique()->nullable();
            $table->decimal('total_item_price', 16, 2)->default(0);
            $table->decimal('total_delivery_amount', 16, 2)->default(0);
            $table->decimal('total_amount_to_collect', 16, 2)->default(0);
            $table->decimal('total_discount_amount', 16, 2)->default(0);
            $table->decimal('total_coupon_amount', 16, 2)->default(0);
            $table->decimal('total_bus_fee', 16, 2)->default(0);
            $table->decimal('transaction_fee', 16, 2)->default(0);
            $table->decimal('insurance_fee', 16, 2)->default(0);
            $table->decimal('warehousing_fee', 16, 2)->default(0);
            $table->decimal('total_agent_fee', 16, 2)->default(0);
            $table->decimal('return_fee', 16, 2)->default(0);
            $table->string('return_type')->nullable();
            $table->decimal('grand_total', 16, 2)->default(0);
            $table->decimal('sender_amount_to_collect', 16, 2)->default(0);
            $table->decimal('receiver_amount_to_collect', 16, 2)->default(0);
            $table->longText('remark')->nullable();
            $table->unsignedInteger('payment_type_id');
            $table->integer('discount_id')->nullable();
            $table->unsignedInteger('origin_city_id')->nullable();
            $table->unsignedInteger('sender_city_id');
            $table->unsignedInteger('receiver_city_id')->nullable();
            $table->unsignedInteger('sender_zone_id')->nullable();
            $table->unsignedInteger('receiver_zone_id')->nullable();
            $table->boolean('bus_station')->default(0);
            $table->integer('sender_bus_station_id')->nullable();
            $table->integer('receiver_bus_station_id')->nullable();
            $table->integer('sender_gate_id')->nullable();
            $table->integer('receiver_gate_id')->nullable();
            $table->boolean('bus_credit')->default(0);
            $table->decimal('bus_fee', 16, 2)->default(0);
            $table->decimal('deposit_amount', 16, 2)->default(0);
            $table->unsignedInteger('call_status_id');
            $table->unsignedInteger('delivery_status_id');
            $table->unsignedInteger('store_status_id');
            $table->timestamp('postpone_date')->nullable();
            $table->timestamp('delivered_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('discount_type')->nullable();
            $table->integer('outgoing_status')->nullable()->default(null);
            $table->boolean('is_closed')->default(0);
            $table->boolean('is_return')->default(0);
            $table->boolean('is_picked')->default(0);
            $table->boolean('is_bus_station_dropoff')->default(0);
            $table->boolean('is_manual_return')->default(0);
            $table->boolean('merchant_payment_status')->default(0);
            $table->boolean('agent_payment_status')->default(0);
            $table->boolean('deli_payment_status')->default(0);
            $table->unsignedInteger('delivery_counter')->default(0);
            $table->unsignedInteger('delegate_person')->nullable();
            $table->unsignedInteger('delegate_duration_id')->nullable();
            $table->string('thirdparty_invoice')->nullable();
            $table->unsignedInteger('created_by_id')->nullable();
            $table->string('created_by_type');
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('sender_city_id')->references('id')->on('cities')->onDelete('restrict');
            // $table->foreign('receiver_city_id')->references('id')->on('cities')->onDelete('restrict');
            // $table->foreign('sender_zone_id')->references('id')->on('zones')->onDelete('restrict');
            // $table->foreign('receiver_zone_id')->references('id')->on('zones')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('vouchers');
        Schema::enableForeignKeyConstraints();
    }
}
