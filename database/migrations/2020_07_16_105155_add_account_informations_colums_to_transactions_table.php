<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountInformationsColumsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedInteger('bank_id')->nullable()->after('note');
            $table->string('account_name')->nullable()->after('bank_id');
            $table->string('account_no')->nullable()->after('account_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['bank_id']);
            $table->dropColumn(['account_name']);
            $table->dropColumn(['account_no']);
        });
    }
}
