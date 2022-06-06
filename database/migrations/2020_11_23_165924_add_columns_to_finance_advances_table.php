<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToFinanceAdvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_advances', function (Blueprint $table) {
            $table->boolean('status')->default(0);
            $table->unsignedInteger('from_finance_account_id');
            $table->unsignedInteger('to_finance_account_id');
            $table->unsignedInteger('staff_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_advances', function (Blueprint $table) {
            $table->dropColumn(['from_finance_account_id','to_finance_account_id','staff_id','status']);
        });
    }
}
