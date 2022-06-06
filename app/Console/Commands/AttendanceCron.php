<?php

namespace App\Console\Commands;

use App\Models\Staff;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AttendanceCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info("Cron is working fine! at daily 23:55");
     
        /*
           Write your database logic we bellow:
        */
        //$staff = Staff::where('is_present', '=', 1)->update(array('is_present' => 0));

        //\Log::info($staff);

      
        $this->info('Attendance:Cron Cummand Run successfully!');

    }
}
