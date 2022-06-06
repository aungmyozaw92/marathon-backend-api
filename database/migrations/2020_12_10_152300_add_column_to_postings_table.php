<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToPostingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_postings', function (Blueprint $table) {
            $table->string('posting_invoice')->unique()->nullable()->after('id');
            $table->string('from_actor_type')->nullable();
            $table->unsignedInteger('from_actor_type_id')->nullable();
            $table->string('to_actor_type')->nullable();
            $table->unsignedInteger('to_actor_type_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_postings', function (Blueprint $table) {
            $table->dropColumn(['posting_invoice','from_actor_type','from_actor_type_id',
            'to_actor_type','to_actor_type_id']);
        });
    }
}
