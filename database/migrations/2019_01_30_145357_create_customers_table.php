<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('other_phone')->nullable();
            $table->longText('address')->nullable();
            $table->bigInteger('point')->nullable();
            $table->string('phone_confirmation_token')->nullable();
            $table->unsignedInteger('city_id')->nullable();
            $table->unsignedInteger('zone_id')->nullable();
            $table->unsignedInteger('badge_id')->nullable();
            $table->Integer('order')->default(0);
            $table->Integer('success')->default(0);
            $table->Integer('return')->default(0);
            $table->decimal('rate', 16, 2)->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('restrict');
            $table->foreign('badge_id')->references('id')->on('badges')->onDelete('restrict');
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
        Schema::dropIfExists('customers');
        Schema::enableForeignKeyConstraints();
    }
}
