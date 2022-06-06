<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMorphsToMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedInteger('messenger_id')->nullable();
            $table->string('messenger_type')->nullable()->after('messenger_id');
            $table->dropForeign('messages_staff_id_foreign');
            // $table->unsignedInteger('staff_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['messenger_id']);
            $table->dropColumn(['messenger_type']);
            // $table->unsignedInteger('staff_id')->nullable(false)->change();
            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('restrict');
        });
    }
}
