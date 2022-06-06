<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIncrementalColumnsToMerchantRateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_rate_cards', function (Blueprint $table) {
            $table->decimal('incremental_weight', 16, 2)->default(0)->after('to_weight');
            $table->decimal('incremental_lwh', 16, 2)->default(0)->after('to_weight');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_rate_cards', function (Blueprint $table) {
            $table->dropColumn(['incremental_weight','incremental_lwh']);
        });
    }
}
