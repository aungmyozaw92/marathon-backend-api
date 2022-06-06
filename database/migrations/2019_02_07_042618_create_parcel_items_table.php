<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParcelItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parcel_id');
            $table->string('item_name');
            $table->integer('item_qty');
            $table->decimal('item_price', 16, 2);
            // $table->decimal('total_amount', 16, 2);
            $table->unsignedInteger('item_status')->nullable();
            // $table->decimal('delivery_amount', 16, 2);
            // $table->decimal('handling_fee', 16, 2);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('parcel_id')->references('id')->on('parcels')->onDelete('restrict');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('parcel_items');
        Schema::enableForeignKeyConstraints();
    }
}
