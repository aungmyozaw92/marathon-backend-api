<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMorphToAllHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pickup_histories', function (Blueprint $table) {
            // $table->dropForeign('pickup_histories_created_by_foreign');
            $table->string('created_by_type')->nullable()->after('created_by');
        });
        Schema::table('voucher_histories', function (Blueprint $table) {
            // $table->dropForeign('voucher_histories_created_by_foreign');
            $table->string('created_by_type')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pickup_histories', function (Blueprint $table) {
            $table->dropColumn(['created_by_type']);
        });
        Schema::table('voucher_histories', function (Blueprint $table) {
            $table->dropColumn(['created_by_type']);
        });
    }
}
