<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('city_id');
            $table->string('sender_type');
            $table->unsignedInteger('sender_id');
            $table->unsignedInteger('sender_associate_id')->nullable();
            //$table->unsignedInteger('receiver_id')->nullable();
            $table->string('pickup_invoice')->unique()->nullable();
            $table->integer('qty')->nullable()->default(0);
            //$table->decimal('total_delivery_amount', 16, 2)->default(0)->nullable(); // need to check and remove
            $table->decimal('total_amount_to_collect', 16, 2)->default(0)->nullable(); // need to check and remove
            $table->decimal('pickup_fee', 16, 2)->default(0);
            $table->longText('note')->nullable();
            //$table->unsignedInteger('type')->nullable(); // need to check and remove
            $table->boolean('is_closed')->default(0);
            $table->boolean('is_paid')->default(0);
            $table->boolean('is_pickuped')->default(0);
            $table->boolean('is_called')->default(0);
            $table->unsignedInteger('priority')->default(0);
            $table->timestamp('pickup_date')->nullable();
            $table->timestamp('requested_date')->nullable();
            $table->unsignedInteger('created_by_id')->nullable();
            $table->string('created_by_type');
            $table->unsignedInteger('pickuped_by_id')->nullable()->default(null);
            $table->string('pickuped_by_type')->nullable()->default(null);
            $table->unsignedInteger('assigned_by_id')->nullable()->default(null);
            $table->string('assigned_by_type')->nullable()->default(null);
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('opened_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('created_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('updated_by')->references('id')->on('staffs')->onDelete('restrict');
            // $table->foreign('deleted_by')->references('id')->on('staffs')->onDelete('restrict');
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
        Schema::dropIfExists('pickups');
        Schema::enableForeignKeyConstraints();
    }
}
