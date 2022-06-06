<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->decimal('static_price_same_city', 16, 2)->after('staff_id')->nullable()->default(null);
            $table->decimal('static_price_diff_city', 16, 2)->after('staff_id')->nullable()->default(null);
            $table->decimal('static_price_branch', 16, 2)->after('staff_id')->nullable()->default(null);
            $table->boolean('is_corporate_merchant')->after('staff_id')->nullable()->default(0);
            $table->string('facebook')->after('staff_id')->nullable()->default(null);
            $table->longText('facebook_url')->after('staff_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn(['static_price_same_city']);
            $table->dropColumn(['static_price_diff_city']);
            $table->dropColumn(['static_price_branch']);
            $table->dropColumn(['is_corporate_merchant']);
            $table->dropColumn(['facebook']);
            $table->dropColumn(['facebook_url']);
        });
    }
}
