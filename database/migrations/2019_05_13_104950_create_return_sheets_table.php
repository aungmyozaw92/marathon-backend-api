<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturnSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_sheets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('return_sheet_invoice')->unique()->nullable();
            $table->unsignedInteger('merchant_id');
            $table->unsignedInteger('merchant_associate_id')->nullable()->default(null);
            $table->integer('qty');
            $table->decimal('sender_amount_to_collect', 16, 2)->default(0);
            $table->decimal('receiver_amount_to_collect', 16, 2)->default(0);
            $table->decimal('credit', 16, 2)->default(0);
            $table->decimal('debit', 16, 2)->default(0);
            $table->decimal('balance', 16, 2)->default(0);
            $table->boolean('is_paid')->default(0);
            $table->unsignedInteger('delivery_id');
            $table->boolean('is_returned')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('restrict');
            $table->foreign('delivery_id')->references('id')->on('staffs')->onDelete('restrict');
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
        Schema::dropIfExists('return_sheets');
        Schema::enableForeignKeyConstraints();
    }
}
