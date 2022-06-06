<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToFinanceAdvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_advances', function (Blueprint $table) {
            $table->boolean('is_approved')->default(0);
            $table->boolean('is_paid')->default(0);
            $table->decimal('total_expense', 16, 2)->default(0);
            $table->decimal('total_advance', 16, 2)->default(0);
            $table->decimal('refund_reimbursements', 16, 2)->default(0);
            $table->unsignedInteger('finance_expense_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_advances', function (Blueprint $table) {
            $table->dropColumn(['is_approved','is_paid','total_expense','total_advance','refund_reimbursements','finance_expense_id']);
        });
    }
}
