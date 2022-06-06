<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceExpenseItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_expense_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('spend_at');
            $table->string('description')->nullable();
            $table->integer('qty')->default(0);
            //$table->unsignedInteger('finance_account_id');
            $table->decimal('amount', 16, 2)->default(0);
            $table->unsignedInteger('finance_expense_id');
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
        Schema::dropIfExists('finance_expense_items');
        Schema::enableForeignKeyConstraints();
    }
}
