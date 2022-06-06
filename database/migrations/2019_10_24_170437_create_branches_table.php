<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->unsignedInteger('city_id');
            $table->unsignedInteger('zone_id');
            $table->string('username')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('lang')->nullable();
            $table->string('currency')->nullable();
            $table->string('pronoun_male')->nullable();
            $table->string('pronoun_female')->nullable();
            $table->string('decimal')->nullable();
            $table->decimal('login', 16, 2)->default(0);
            $table->string('datetime')->nullable();
            $table->decimal('pickup_fee', 16, 2)->default(0);
            $table->integer('pickup_min_qty')->nullable();
            $table->string('scale_unit')->nullable();
            $table->string('weight_unit')->nullable();
            $table->decimal('dropoff_price', 16, 2)->default(0);
            $table->decimal('target_sale', 16, 2)->default(0);
            $table->decimal('target_coupon', 16, 2)->default(0);
            $table->string('target_start_date')->nullable();
            $table->string('target_end_date')->nullable();

            $table->decimal('transition_amout', 16, 2)->default(0);
            $table->decimal('transition_fee', 16, 2)->default(0);
            $table->decimal('insurance_fee', 16, 2)->default(0);
            $table->decimal('warehouse_fee', 16, 2)->default(0);
            $table->decimal('agent_fee_base_rate', 16, 2)->default(0);
            $table->decimal('rounding', 16, 2)->default(0);
            $table->integer('return_percentage')->nullable();

            $table->decimal('lunch', 16, 2)->default(0);
            $table->decimal('delivery_commission', 16, 2)->default(0);
            $table->decimal('pickup_commission', 16, 2)->default(0);
            $table->integer('postpone_day')->nullable();
            $table->decimal('postpone_fee', 16, 2)->default(0);
            $table->decimal('immediately_return_fee', 16, 2)->default(0);
            $table->string('attendance')->nullable();
            $table->integer('duration')->nullable();
            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();

            $table->foreign('city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('restrict');
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
        Schema::dropIfExists('branches');
        Schema::enableForeignKeyConstraints();
    }
}
