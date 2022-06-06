<?php

namespace App\Http\Resources\AgentBadge;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentBadgeResource extends JsonResource
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
            'id'                   => $this->id,
            'name'                 => $this->name,
            'deposit'              => $this->deposit,
            'logo'                 => Storage::url('agent_badge_logo/' . $this->logo),
            'monthly_reward'       => $this->monthly_reward,
            'delivery_points'      => $this->delivery_points,
            'weekly_payment'       => $this->weekly_payment,
            'monthly_good_credit'  => $this->monthly_good_credit,
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
