<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedInteger('finance_nature_id');
            $table->unsignedInteger('finance_master_type_id');
            $table->unsignedInteger('finance_account_type_id');
            $table->unsignedInteger('finance_group_id');
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('finance_tax_id');
            $table->unsignedInteger('finance_code_id');
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('finance_accounts');
        Schema::enableForeignKeyConstraints();
    }
}
