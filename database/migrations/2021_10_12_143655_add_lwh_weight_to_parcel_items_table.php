<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLwhWeightToParcelItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parcel_items', function (Blueprint $table) {
            $table->decimal('weight', 16, 3)->default(0);
            $table->decimal('lwh', 16, 3)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parcel_items', function (Blueprint $table) {
            $table->dropColumn(['weight','lwh']);
        });
    }
}
