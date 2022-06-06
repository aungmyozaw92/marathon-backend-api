<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deli_sheets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('delisheet_invoice')->unique()->nullable();
            $table->integer('qty');
            $table->unsignedInteger('zone_id');
            $table->unsignedInteger('delivery_id');
            $table->unsignedInteger('staff_id');
            $table->longText('note')->nullable();
            // $table->unsignedInteger('priority');
            $table->decimal('lunch_amount', 16, 2)->default(0);
            $table->decimal('commission_amount', 16, 2)->default(0);
            $table->decimal('collect_amount', 16, 2)->default(0);
            $table->decimal('total_amount', 16, 2)->default(0);
            $table->boolean('is_closed')->default(0);
            $table->boolean('is_paid')->default(0);
            $table->boolean('is_scanned')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('restrict');
            $table->foreign('delivery_id')->references('id')->on('staffs')->onDelete('restrict');
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
        Schema::dropIfExists('deli_sheets');
        Schema::enableForeignKeyConstraints();
    }
}
