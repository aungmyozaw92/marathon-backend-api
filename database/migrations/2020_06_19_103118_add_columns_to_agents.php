<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToAgents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->unsignedInteger('agent_badge_id')->nullable()->after('is_active');
            $table->decimal('rewards', 16, 2)->default(0)->after('agent_badge_id');
            $table->boolean('is_positive_monthly')->default(0)->after('rewards');
            $table->decimal('monthly_collected_amount', 16, 2)->default(0)->after('is_positive_monthly');
            $table->decimal('weekly_collected_amount', 16, 2)->default(0)->after('monthly_collected_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn(['agent_badge_id']);
            $table->dropColumn(['rewards']);
            $table->dropColumn(['is_positive_monthly']);
            $table->dropColumn(['monthly_collected_amount']);
            $table->dropColumn(['weekly_collected_amount']);
        });
    }
}
