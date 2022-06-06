<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('asset_type_id');
            $table->string('description')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('asset_no')->unique()->nullable();;
            $table->decimal('purchase_price', 16, 2)->default(0);
            $table->date('purchase_date');
            $table->date('depreciation_start_date');
            $table->integer('warranty_month');
            $table->integer('depreciation_month');
            $table->string('depreciation_rate');
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
        Schema::dropIfExists('finance_assets');
        Schema::enableForeignKeyConstraints();
    }
}
