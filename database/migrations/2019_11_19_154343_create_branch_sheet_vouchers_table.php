<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchSheetVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_sheet_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('branch_sheet_id');
            $table->unsignedInteger('voucher_id');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_sheet_id')->references('id')->on('branch_sheets')->onDelete('restrict');
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
        Schema::dropIfExists('branch_sheet_vouchers');
    }
}
