<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToInvoiceJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_journals', function (Blueprint $table) {
            $table->decimal('diff_adjustment_amount', 16, 2)->default(0)->after('adjustment_amount');
            $table->string('adjustment_by_name')->nullable()->after('adjustment_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_journals', function (Blueprint $table) {
            $table->dropColumn(['diff_adjustment_amount', 'adjustment_by_name']);
        });
    }
}
