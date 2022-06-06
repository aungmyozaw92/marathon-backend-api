<?php

namespace App\Http\Resources\Mobile\Agent;

use App\Models\Journal;
use App\Models\Waybill;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Account\AccountResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AgentBadge\AgentBadgeResource;

class AgentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $attachment = $this->attachments;
        if ($attachment->count() > 0) {
            $url = Storage::url('agent' . '/' . $attachment[0]->image);
        } else {
            $url = null;
        }
        $incoming_waybill_count = Waybill::where('to_city_id', auth()->user()->city_id)
                            ->where('is_received', 0)->count();
        $total_commission_amount = Journal::filterAgentCreditLists(['start_date'=>''])->sum('amount');
        $total_collected_amount = Journal::filterAgentDebitLists(['start_date'=>''])->sum('amount');
        $pending_topup_amount = 0;
        $pending_withdraw_amount = 0;
        if ($this->account) {
            $transactions = Transaction::where('status', 0)
            ->where(function ($q) {
                $q->where('from_account_id', $this->account->id)
                                    ->orWhere('to_account_id', $this->account->id);
            })->get();
                                    
            $pending_topup_amount = $transactions->where('type', 'Topup')->sum('amount');
            $pending_withdraw_amount = $transactions->where('type', 'Withdraw')->sum('amount');
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'phone'   => $this->phone,
            'address'   => $this->address,
            'balance'  => $this->account? $this->account->balance : 0,
            'pending_balance'  => $pending_topup_amount  - $pending_withdraw_amount,
            'total_commission_amount' => $total_commission_amount,
            'total_collected_amount' => $total_collected_amount,
            'incoming_waybill_count'  => $incoming_waybill_count,
            'on_demand' => $this->on_demand,
            'agent_branch' => $this->agent_branch,
            'image_url' => $url,
            'delivery_commission' => $this->delivery_commission,
            'rewards'                   => $this->rewards,
            'is_positive_monthly'       => $this->is_positive_monthly,
            'monthly_collected_amount'  => $this->monthly_collected_amount,
            'weekly_collected_amount'   => $this->weekly_collected_amount,
            'is_active'                 => $this->is_active,
            'shop_name'                 => $this->shop_name,
            //'total_balance' => ($this->account) ? $this->account->balance : 0.00,
            //'account' => AccountResource::make($this->whenLoaded('account')),
            'city' => CityResource::make($this->whenLoaded('city')),
            'agent_badge'   => AgentBadgeResource::make($this->whenLoaded('agent_badge'))
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => 1,
        ];
    }
}
