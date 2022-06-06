<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('journal_no')->unique()->nullable();
            $table->unsignedInteger('debit_account_id');
            $table->unsignedInteger('credit_account_id');
            $table->decimal('amount', 16, 2)->default(0);
            $table->string('resourceable_type');
            $table->unsignedInteger('resourceable_id');
            $table->integer('status')->default(0);
            $table->integer('balance_status')->default(0);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journals');
    }
}
