<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFirestoreDocumentColumnsForMerchantpp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('cities', function (Blueprint $table) {
            $table->longText('firestore_document')->nullable()->after('id');
        });
        Schema::table('zones', function (Blueprint $table) {
            $table->longText('firestore_document')->nullable()->after('id');
        });
        Schema::table('global_scales', function (Blueprint $table) {
            $table->longText('firestore_document')->nullable()->after('id');
        });
        Schema::table('payment_types', function (Blueprint $table) {
            $table->longText('firestore_document')->nullable()->after('id');
        });
        Schema::table('pickups', function (Blueprint $table) {
            $table->longText('firestore_document')->nullable()->after('id');
        });
        Schema::table('vouchers', function (Blueprint $table) {
            $table->longText('firestore_document')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['firestore_document']);
        });
        Schema::table('zones', function (Blueprint $table) {
            $table->dropColumn(['firestore_document']);
        });
        Schema::table('global_scales', function (Blueprint $table) {
            $table->dropColumn(['firestore_document']);
        });
        Schema::table('payment_types', function (Blueprint $table) {
            $table->dropColumn(['firestore_document']);
        });
        Schema::table('pickups', function (Blueprint $table) {
            $table->dropColumn(['firestore_document']);
        });
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['firestore_document']);
        });
    }
}
