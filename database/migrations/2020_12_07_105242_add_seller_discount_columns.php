<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSellerDiscountColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->decimal('seller_discount', 16, 2)->default(0);
        });
        Schema::table('vouchers', function (Blueprint $table) {
            $table->decimal('seller_discount', 16, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->dropColumn(['seller_discount']);
        });
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['seller_discount']);
        });
    }
}
