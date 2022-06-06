<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->date('spend_on');
            // $table->increments('attachments');
            $table->decimal('total', 16, 2)->default(0);
            $table->decimal('sub_total', 16, 2)->default(0);
            $table->unsignedInteger('branch_id')->nullable();
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
        Schema::dropIfExists('finance_expenses');
        Schema::enableForeignKeyConstraints();
    }
}
