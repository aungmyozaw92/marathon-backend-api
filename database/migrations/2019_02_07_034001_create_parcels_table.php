<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('voucher_id');
            $table->decimal('weight', 16, 2)->default(0);
            $table->unsignedInteger('global_scale_id');
            $table->unsignedInteger('discount_type_id')->nullable();
            $table->unsignedInteger('coupon_associate_id')->nullable();

            $table->decimal('cal_parcel_price', 16, 2)->default(0);
            $table->decimal('cal_delivery_price', 16, 2)->default(0);
            $table->decimal('cal_gate_price', 16, 2)->default(0);

            $table->decimal('discount_price', 16, 2)->default(0);
            $table->decimal('coupon_price', 16, 2)->default(0);
            $table->decimal('agent_fee', 16, 2)->default(0);
            
            $table->decimal('label_parcel_price', 16, 2)->default(0);
            $table->decimal('label_delivery_price', 16, 2)->default(0);
            $table->decimal('label_gate_price', 16, 2)->default(0);

            $table->decimal('sub_total', 16, 2)->default(0);

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('restrict');
            // $table->foreign('global_scale_id')->references('id')->on('global_scales')->onDelete('restrict');
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
        Schema::dropIfExists('parcels');
        Schema::enableForeignKeyConstraints();
    }
}
