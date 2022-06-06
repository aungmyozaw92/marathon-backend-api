<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoiceToFinanceExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_expenses', function (Blueprint $table) {
            $table->string('expense_invoice')->unique()->nullable()->after('id');
        });
        if (Schema::hasColumn('finance_expenses', 'sub_total')) {
            Schema::table('finance_expenses', function (Blueprint $table) {
                $table->dropColumn('sub_total');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('finance_expenses', 'expense_invoice')) {
            Schema::table('finance_expenses', function (Blueprint $table) {
                $table->dropColumn('expense_invoice');
            });
        }
    }
}
