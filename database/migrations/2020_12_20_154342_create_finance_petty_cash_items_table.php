<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinancePettyCashItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_petty_cash_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_no')->unique()->nullable();
            $table->string('spend_at');
            $table->string('description')->nullable();
            $table->string('remark')->nullable();
            $table->decimal('amount', 16, 2)->default(0);
            $table->decimal('tax_amount', 16, 2)->default(0);
            $table->unsignedInteger('from_finance_account_id');
            $table->unsignedInteger('to_finance_account_id');
            $table->unsignedInteger('finance_petty_cash_id');
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
        Schema::dropIfExists('finance_petty_cash_items');
    }
}
