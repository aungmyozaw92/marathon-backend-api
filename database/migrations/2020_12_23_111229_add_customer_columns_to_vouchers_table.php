<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerColumnsToVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('receiver_name')->nullable()->after('receiver_id');
            $table->string('receiver_phone')->nullable()->after('receiver_name');
            $table->string('receiver_other_phone')->nullable()->after('receiver_phone');
            $table->longText('receiver_address')->nullable()->after('receiver_other_phone');
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
            $table->dropColumn(['receiver_name','receiver_phone','receiver_other_phone','receiver_address']);
        });
    }
}
