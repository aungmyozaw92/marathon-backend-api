<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_sheets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('merchantsheet_invoice')->unique()->nullable();
            $table->unsignedInteger('merchant_id');
            $table->unsignedInteger('merchant_associate_id')->nullable()->default(null);
            $table->integer('qty');
            $table->longText('note')->nullable();
            $table->decimal('sender_amount_to_collect', 16, 2)->default(0);
            $table->decimal('receiver_amount_to_collect', 16, 2)->default(0);
            $table->decimal('credit', 16, 2)->default(0);
            $table->decimal('debit', 16, 2)->default(0);
            $table->decimal('balance', 16, 2)->default(0);
            $table->boolean('is_paid')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('restrict');
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
        Schema::dropIfExists('merchant_sheets');
        Schema::enableForeignKeyConstraints();
    }
}
