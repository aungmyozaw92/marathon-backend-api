<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToAccountToFinanceConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_configs', function (Blueprint $table) {
            $table->unsignedInteger('to_finance_account_id')->nullable()->after('finance_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_configs', function (Blueprint $table) {
            $table->dropColumn(['to_finance_account_id']);
        });
    }
}
