<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReturningsToVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->date('pending_returning_date')->nullable()->after('returned_date');
            $table->string('pending_returning_actor_type')->nullable()->after('pending_returning_date');
            $table->unsignedInteger('pending_returning_actor_id')->nullable()->after('pending_returning_actor_type');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['pending_returning_actor_type','pending_returning_actor_id','pending_returning_date']);
        });
    }
}
