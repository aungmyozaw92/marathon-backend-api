<?php

namespace App\Console;

use App\Console\Commands\EndofdayCron;
use App\Console\Commands\AttendanceCron;
use App\Console\Commands\TelescopePrune;
use App\Console\Commands\HeroMonthlyBonusCron;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\AgentWeeklyBonusCron;
use App\Console\Commands\AgentMonthlyBonusCron;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        TelescopePrune::class,
        AttendanceCron::class,
        AgentWeeklyBonusCron::class,
        AgentMonthlyBonusCron::class,
        HeroMonthlyBonusCron::class,
    ];

    /**
     * Define the application'StoreStock
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('telescope:prune')->weekly();
        $schedule->command('attendance:cron')->daily();
        $schedule->command('agentweeklybonus:cron')->weeklyOn(1, '17:00');
        $schedule->command('agentmonthlybonus:cron')->monthly();
        $schedule->command('heromonthlybonus:cron')->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
