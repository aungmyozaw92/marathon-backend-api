<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staffs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('role_id');
            $table->integer('department_id')->unsigned();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('phone')->unique()->nullable();
            $table->longText('token')->nullable();
            $table->longText('device_token')->nullable();
            $table->boolean('is_present')->default(1);
            $table->unsignedInteger('city_id');
            $table->unsignedInteger('zone_id')->nullable()->default(null);
            $table->decimal('points', 16, 2)->default(0);
            $table->unsignedInteger('courier_type_id')->nullable()->default(null);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict');
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('restrict');
            $table->foreign('courier_type_id')->references('id')->on('courier_types')->onDelete('restrict');
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
        Schema::dropIfExists('staffs');
        Schema::enableForeignKeyConstraints();
    }
}
