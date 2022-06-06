<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoucherAssociatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_associates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('voucher_id');
            $table->decimal('delivery_amount');
            $table->decimal('item_price');
            $table->decimal('total');
            $table->string('status')->unique();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            //$table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('restrict');
            // $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
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
        Schema::dropIfExists('voucher_associates');
        Schema::enableForeignKeyConstraints();
    }
}
