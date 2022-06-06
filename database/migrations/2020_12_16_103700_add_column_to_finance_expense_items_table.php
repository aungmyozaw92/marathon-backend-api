<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToFinanceExpenseItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_expense_items', function (Blueprint $table) {
            $table->decimal('tax_amount', 16, 2)->default(0);
            $table->string('remark')->nullable();
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
            $table->dropColumn(['tax_amount','remark']);
        });
    }
}
