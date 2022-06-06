<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToFinanceExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_expenses', function (Blueprint $table) {
            $table->boolean('is_approved')->default(0);
            $table->string('fn_paymant_option')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_expenses', function (Blueprint $table) {
            $table->dropColumn(['is_approved','fn_paymant_option']);
        });
    }
}
