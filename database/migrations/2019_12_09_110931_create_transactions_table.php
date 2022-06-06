<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transaction_no')->unique();
            $table->unsignedInteger('from_account_id');
            $table->unsignedInteger('to_account_id');
            $table->bigInteger('amount')->default(0);
            $table->bigInteger('extra_amount')->default(0);
            $table->string('type');
            $table->boolean('status')->default(0);
            $table->longText('note')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('from_account_id')->references('id')->on('accounts')->onDelete('restrict');
            $table->foreign('to_account_id')->references('id')->on('accounts')->onDelete('restrict');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
