<?php

namespace App\Http\Resources\Account;

use App\Http\Resources\City\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    private $total_balance = true;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->accountable_type == 'HQ') {
            $accountable = 'HQ';
        } else {
            $accountable = $this->accountable->only(["id",
                            "name",
                            "username",
                            "city_id",
                            "phone",
                            "address",
                            "on_demand",
                            "agent_branch",
                            "delivery_commission",
                            "is_active",
                            "shop_name",
                            "agent_badge_id",
                            "rewards"]);
        }

        // $total_balance += $this->balance;
        return [
            'id' => $this->id,
            'account_no' => $this->account_no,
            'accountable_type' => $this->accountable_type,
            'accountable_id' => $this->accountable_id,
            // 'balance' => number_format($this->balance),
            'balance' => $this->balance,
            'city' => CityResource::make($this->whenLoaded('city')),
            'accountable' => $accountable,

            // 'pending_balance' => number_format($this->pending_balance())
            'pending_balance' => $this->pending_balance()
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
