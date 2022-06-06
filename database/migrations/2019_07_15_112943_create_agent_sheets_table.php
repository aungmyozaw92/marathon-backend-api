<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_sheets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('agentsheet_invoice')->unique()->nullable();
            $table->unsignedInteger('agent_id');
            $table->integer('qty');
            //$table->decimal('sender_amount_to_collect', 16, 2)->default(0);
            $table->decimal('total_commission_amount', 16, 2)->default(0);
            $table->decimal('credit', 16, 2)->default(0);
            $table->decimal('debit', 16, 2)->default(0);
            $table->decimal('balance', 16, 2)->default(0);
            $table->boolean('is_paid')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_sheets');
    }
}
