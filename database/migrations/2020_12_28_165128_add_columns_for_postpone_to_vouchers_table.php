<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsForPostponeToVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->date('postpone_perform_date')->nullable()->after('postpone_date');
            $table->string('postpone_actor_type')->nullable()->after('postpone_perform_date');
            $table->unsignedInteger('postpone_actor_id')->nullable()->after('postpone_actor_type');
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
            $table->dropColumn(['postpone_actor_type', 'postpone_actor_id', 'postpone_perform_date']);
        });
    }
}
