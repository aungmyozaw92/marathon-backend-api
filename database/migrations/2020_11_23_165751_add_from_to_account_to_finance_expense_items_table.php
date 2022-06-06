<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFromToAccountToFinanceExpenseItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_expense_items', function (Blueprint $table) {
            $table->unsignedInteger('from_finance_account_id')->nullable();
            $table->unsignedInteger('to_finance_account_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_expense_items', function (Blueprint $table) {
            $table->dropColumn(['from_finance_account_id','to_finance_account_id']);
        });
    }
}
