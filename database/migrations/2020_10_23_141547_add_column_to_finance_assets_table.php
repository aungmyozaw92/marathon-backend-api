<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToFinanceAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_assets', function (Blueprint $table) {
            $table->unsignedInteger('accumulated_depreciation_account_id');
            $table->unsignedInteger('depreciation_expense_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_assets', function (Blueprint $table) {
            $table->dropColumn(['accumulated_depreciation_account_id','depreciation_expense_account_id']);
        });
    }
}
