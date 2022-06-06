<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReceivedDateAndReceivedByTypeAndReceivedByIdToWaybills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('waybills', function (Blueprint $table) {
            $table->timestamp('received_date')->nullable();
            $table->unsignedInteger('received_by_id')->nullable();
            $table->string('received_by_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('waybills', function (Blueprint $table) {
            $table->dropColumn(['received_date']);
            $table->dropColumn(['received_by_id']);
            $table->dropColumn(['received_by_type']);
        });
    }
}
