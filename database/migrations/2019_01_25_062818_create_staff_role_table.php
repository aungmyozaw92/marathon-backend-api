<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_role', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staff_id');
            $table->integer('role_id');
            $table->timestamps();

            // $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('CASCADE');
           // $table->foreign('role_id')->references('id')->on('roles')->onDelete('CASCADE');
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
        Schema::dropIfExists('staff_role');
        Schema::enableForeignKeyConstraints();
    }
}
