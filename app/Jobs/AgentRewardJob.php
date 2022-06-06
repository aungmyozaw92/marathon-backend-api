<?php

namespace App\Jobs;

use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AgentRewardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $agent;
    protected $amount_to_collect;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Agent $agent, $amount_to_collect)
    {
        $this->agent = $agent;
        $this->amount_to_collect = $amount_to_collect;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $total_balance = $this->agent->account->balance + $this->agent->pending_balance();
        // \Log::info("Start AgentRewardJob");
        // \Log::info($total_balance);
        // \Log::info($this->agent->account->balance);
        // \Log::info($this->agent->pending_balance());
        $agent_badge = $this->agent->agent_badge;
        if ($total_balance > 0) {
            $this->agent->monthly_collected_amount += $agent_badge->monthly_good_credit * $this->amount_to_collect;
            // \Log::info($this->agent);
            // \Log::info($this->amount_to_collect);
            // \Log::info($agent_badge->monthly_good_credit * $this->amount_to_collect);
        }
            $this->agent->weekly_collected_amount += $agent_badge->weekly_payment * $this->amount_to_collect;
            $this->agent->save();
        // \Log::info("AgentRewardJob");
    }
}
