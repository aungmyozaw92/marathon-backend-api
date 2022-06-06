<?php

namespace App\Console\Commands;

use App\Models\HeroBadge;
use Illuminate\Console\Command;
use App\Repositories\Web\Api\v1\JournalRepository;
use App\Repositories\Web\Api\v1\TransactionRepository;
use App\Models\Staff;
use App\Models\HeroPoint;
use App\Contracts\MembershipContract;

class HeroMonthlyBonusCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'heromonthlybonus:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hero Monthly Bonus Calculation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     parent::__construct();
    // }
    protected $membershipContract;
    public function __construct(MembershipContract $membershipContract)
    {
        parent::__construct();
        $this->membershipContract = $membershipContract;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        \Log::info("Start Hero Monthly Bonus");
        $journalRepository = new JournalRepository();
        $heroes = Staff::where([['department_id', 5], ['role_id', 5]])->get();
        foreach ($heroes as $hero) {
            $branch_account = $hero->city->branch->account->id;
            $hero_account = $hero->account ? $hero->account : $journalRepository->create_account($hero, 'Staff');
            if ($hero->points >= 500) {
                if ($hero->points >= 1750) {
                    $monthly_bonus = HeroPoint::where('start_point', '=', 1500)->first()->bonus;
                } else {
                    $monthly_bonus = HeroPoint::where('start_point', '<=', $hero->points)->where('end_point', '>=', $hero->points)->first()->bonus;
                }
                $loggable_badge = $hero->hero_badge ? $hero->hero_badge->name : 'New';
                \Log::info(print_r($hero->name . '->' . $hero->points . '->' . $loggable_badge . '->' . $monthly_bonus, true) . " = Success");
                $journal = $journalRepository->JournalCreateData($branch_account, $hero_account->id, $monthly_bonus, $hero, 'Staff', 1);
                $this->membershipContract->rebornHero($hero, $hero->journals()->latest()->first());
            } else {
                $loggable_badge = $hero->hero_badge ? $hero->hero_badge->name : 'New';
                if ($hero->hero_badge) {
                    $hero->hero_badge_id =  $hero->hero_badge_id > 1 ? $hero->hero_badge_id - 1 : 1;
                }
                $hero->points = 0;
                $hero->save();
                \Log::info(print_r($hero->name . '->' . $hero->points . '->' . $loggable_badge, true) . " = Fail");
            }
        }

        \Log::info("End Hero Monthly Bonus");
    }
}
