<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceTableOfAuthoritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_table_of_authorities', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('petty_amount', 16, 2)->default(0);
            $table->decimal('expense_amount', 16, 2)->default(0);
            $table->decimal('advance_amount', 16, 2)->default(0);
            $table->unsignedInteger('staff_id')->nullable();
            $table->unsignedInteger('manager_id')->nullable();
            $table->boolean('is_need_approve')->default(false);
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
        Schema::dropIfExists('finance_table_of_authorities');
    }
}
