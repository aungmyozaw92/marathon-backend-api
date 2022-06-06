<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_journals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('journal_no')->unique()->nullable();
            $table->unsignedInteger('merchant_id')->nullable();
            $table->unsignedInteger('city_id');
            $table->unsignedInteger('debit_account_id');
            $table->unsignedInteger('credit_account_id');
            $table->decimal('amount', 16, 2)->default(0);
            $table->string('resourceable_type');
            $table->unsignedInteger('resourceable_id');
            $table->boolean('status')->default(0);
            $table->string('thirdparty_invoice')->nullable();
            $table->string('voucher_no')->nullable();
            $table->date('pickup_date')->nullable();
            $table->date('delivered_date')->nullable();
            $table->string('receiver_name')->nullable();
            $table->text('receiver_address')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('receiver_city')->nullable();
            $table->string('receiver_zone')->nullable();
            $table->decimal('total_amount_to_collect', 16, 2)->nullable();
            $table->text('voucher_remark')->nullable();
            $table->boolean('balance_status')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_journals');
    }
}
