<?php

namespace App\Console\Commands;

use App\Models\Agent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Web\Api\v1\TransactionRepository;

class AgentMonthlyBonusCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agentmonthlybonus:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agent Weekly Bunus Calculation';

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
        \Log::info("Start Agent Monthly Bonus");
        $transactionRepository = new TransactionRepository();
         $agents = Agent::get();
        //  ->where('monthly_collected_amount','>',0)
        //  ->where('is_positive_monthly', 1)
         foreach ($agents as $agent) {
            $total_balance = $agent->account->balance + $agent->pending_balance();

            $data['transaction_no'] = $transactionRepository->get_transaction_id();
            $data['from_account_id'] = 1;
            $data['to_account_id'] = $agent->account->id;
            $data['amount'] = $agent->monthly_collected_amount;
            $data['type'] = "MonthlyBonus";
            $data['status'] = 1;
            $data['note'] = "Agent Monthly Bonus";
            $data['created_by'] = 1;

            if ($total_balance >= 0 && $agent->monthly_collected_amount > 0) {
                $transaction = $transactionRepository->create_transaction($data);
                if ($transaction) {
                    \Log::info("Ok Transaction For Agent Monthly Bonus");
                    $transactionRepository->create_journal($transaction);
                    $transaction->to_account->balance += $transaction->amount+$transaction->extra_amount;
                    $transaction->to_account->save();
                } 
            }
            if ($agent->agent_badge && $agent->agent_badge->monthly_reward > 0) {
                $data['transaction_no'] = $transactionRepository->get_transaction_id();
                $data['amount'] = $agent->agent_badge->monthly_reward;
                $data['type'] = "MembershipReward";
                $data['note'] = "Agent Monthly MembershipReward";
                $transaction = $transactionRepository->create_transaction($data);
                if ($transaction) {
                    \Log::info("Ok Transaction For Agent MembershipReward");
                    $transactionRepository->create_journal($transaction);
                    $transaction->to_account->balance += $transaction->amount+$transaction->extra_amount;
                    $transaction->to_account->save();
                }
            }
         }
        $agent = DB::table('agents')->update
             ([
                // 'is_positive_monthly' => 1, 
                 'monthly_collected_amount' => 0
             ])
            ;
         \Log::info("End Agent Monthly Bonus");
    }
}
