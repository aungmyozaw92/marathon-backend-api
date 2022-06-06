<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToReturnSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('return_sheets', function (Blueprint $table) {
            $table->unsignedInteger('closed_by')->nullable();
            $table->date('closed_date')->nullable();
            $table->boolean('is_closed')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('return_sheets', function (Blueprint $table) {
            $table->dropColumn(['is_closed','closed_date','closed_by']);
        });
    }
}
