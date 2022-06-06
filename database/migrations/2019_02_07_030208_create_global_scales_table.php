<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalScalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_scales', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('cbm', 16, 2);
            $table->decimal('support_weight', 16, 2)->default(0);
            $table->decimal('max_weight', 16, 2)->default(0);
            // $table->decimal('global_scale_rate', 16, 2)->default(0);
            // $table->decimal('global_scale_agent_rate', 16, 2)->default(0);
            // $table->decimal('salt', 16, 2)->default(0);
            $table->longText('description')->nullable();
            $table->longText('description_mm')->nullable();
            // $table->decimal('unit', 16, 2);
            //$table->decimal('bus_fee', 16, 2)->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('global_scales');
        Schema::enableForeignKeyConstraints();
    }
}
