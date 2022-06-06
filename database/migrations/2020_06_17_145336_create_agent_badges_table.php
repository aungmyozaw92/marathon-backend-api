<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentBadgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_badges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->decimal('deposit', 16, 4)->default(0);
            $table->string('logo');
            $table->decimal('monthly_reward', 16, 4)->default(0);
            $table->unsignedInteger('delivery_points')->default(0);
            $table->decimal('weekly_payment', 16, 4)->default(0);
            $table->decimal('monthly_good_credit', 16, 4)->default(0);
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
        Schema::dropIfExists('agent_badges');
    }
}
