<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusSheetVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bus_sheet_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('bus_sheet_id');
            $table->unsignedInteger('voucher_id');
            $table->decimal('actual_bus_fee', 16, 2)->default(null)->nullable();
            // $table->unsignedInteger('payment_status_id')->nullable();
            $table->unsignedInteger('delivery_status_id')->nullable()->default(null);
            $table->boolean('is_return')->default(null)->nullable();
            $table->boolean('is_paid')->default(0);
            $table->longText('note')->nullable()->default(null);
            $table->unsignedInteger('priority')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('bus_sheet_id')->references('id')->on('bus_sheets')->onDelete('restrict');
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('restrict');
            // $table->foreign('payment_status_id')->references('id')->on('payment_statuses')->onDelete('restrict');
            $table->foreign('delivery_status_id')->references('id')->on('delivery_statuses')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bus_sheet_vouchers');
    }
}
