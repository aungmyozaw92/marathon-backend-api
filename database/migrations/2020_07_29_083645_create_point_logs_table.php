<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('staff_id');
            $table->decimal('points', 16, 2)->default(0);
            $table->string('status')->nullable();
            $table->string('resourceable_type');
            $table->unsignedInteger('resourceable_id');
            $table->unsignedInteger('hero_badge_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('hero_badge_id')->references('id')->on('hero_badges')->onDelete('restrict');
            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('restrict');
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
        Schema::dropIfExists('point_logs');
        Schema::enableForeignKeyConstraints();
    }
}
