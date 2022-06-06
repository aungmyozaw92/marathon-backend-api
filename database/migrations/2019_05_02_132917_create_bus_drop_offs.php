<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusDropOffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bus_drop_offs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('gate_id');
            $table->unsignedInteger('global_scale_id');
            $table->unsignedInteger('route_id');
            $table->decimal('base_rate', 16, 2)->default(0);
            $table->decimal('agent_base_rate', 16, 2)->default(0);
            $table->decimal('salt', 16, 2)->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

           // $table->foreign('gate_id')->references('id')->on('gates')->onDelete('restrict');
            $table->foreign('global_scale_id')->references('id')->on('global_scales')->onDelete('restrict');
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('restrict');
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
        Schema::dropIfExists('bus_drop_offs');
        Schema::enableForeignKeyConstraints();
    }
}
