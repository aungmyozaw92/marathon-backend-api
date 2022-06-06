<?php

namespace App\Http\Resources\Agent;

use App\Models\Transaction;
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
        return [
            'id'                        => $this->id,
            'name'                      => $this->name,
            'account_code'              => $this->account_code,
            'username'                  => $this->username,
            'phone'                     => $this->phone,
            'address'                   => $this->address,
            'on_demand'                 => $this->on_demand,
            'agent_branch'              => $this->agent_branch,
            // 'balance'                   => number_format($this->account->balance),
            // 'pending_balance'           => number_format($this->pending_balance()),
            // 'delivery_commission'       => number_format($this->delivery_commission),
            'balance'                   => $this->account->balance,
            'pending_balance'           => $this->pending_balance(),
            'delivery_commission'       => $this->delivery_commission,
            'rewards'                   => $this->rewards,
            'is_positive_monthly'       => $this->is_positive_monthly,
            'monthly_collected_amount'  => $this->monthly_collected_amount,
            'weekly_collected_amount'   => $this->weekly_collected_amount,
            'is_active'                 => $this->is_active,
            'shop_name'                 => $this->shop_name,
            'city'                      => CityResource::make($this->whenLoaded('city')),
            'account'                   => AccountResource::make($this->whenLoaded('account')),
            'agent_badge'               => AgentBadgeResource::make($this->whenLoaded('agent_badge'))
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
