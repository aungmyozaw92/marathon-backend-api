<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsForMerchantappFirestore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('merchants', function (Blueprint $table) {
            $table->longText('firebase_token')->nullable()->after('token');
            $table->longText('firestore_document')->nullable()->after('id');
            $table->unsignedInteger('default_payment_type_id')->nullable()->after('firestore_document');
        });
        Schema::table('product_types', function (Blueprint $table) {
            $table->longText('firestore_document')->nullable()->after('id');
        });
        Schema::table('products', function (Blueprint $table) {
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
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn(['firebase_token', 'firestore_document', 'default_payment_type_id']);
        });
        Schema::table('product_types', function (Blueprint $table) {
            $table->dropColumn(['firestore_document']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['firestore_document']);
        });
    }
}
