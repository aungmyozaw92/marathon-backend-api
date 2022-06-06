<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinancePettyCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_petty_cashes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_no')->unique()->nullable();
            $table->date('spend_on');
            $table->decimal('total', 16, 2)->default(0);
            $table->string('fn_paymant_option')->nullable();
            $table->unsignedInteger('staff_id')->nullable();
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
        Schema::dropIfExists('finance_petty_cashes');
    }
}
