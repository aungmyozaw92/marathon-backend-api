<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToFailureStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('failure_statuses', function (Blueprint $table) {
            $table->boolean('is_show_on_delisheet')->default(true)->after('specification');
            $table->boolean('is_enable_pending_return')->default(false)->after('is_show_on_delisheet');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('failure_statuses', function (Blueprint $table) {
            $table->dropColumn(['is_show_on_delisheet','is_enable_pending_return']);
        });
    }
}
