<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturnSheetVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_sheet_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('return_sheet_id');
            $table->unsignedInteger('voucher_id');
            $table->longText('note')->nullable()->default(null);
            $table->unsignedInteger('priority')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->integer('is_return_fee')->default(1);
            // $table->decimal('compensated_amount', 16, 2)->default(0);
            // $table->decimal('delivery_refund', 16, 2)->default(0);
            // $table->decimal('delivery_return', 16, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('return_sheet_id')->references('id')->on('return_sheets')->onDelete('restrict');
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('return_sheet_vouchers');
    }
}
