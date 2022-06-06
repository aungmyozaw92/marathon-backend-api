<?php

namespace App\Http\Resources\ThirdParty\Zone;

use App\Http\Resources\ThirdParty\City\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ZoneResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'name_mm' => $this->name_mm,
            // 'zone_rate' => $this->zone_rate,
            // 'zone_agent_rate' => $this->zone_agent_rate,
            'city' => CityResource::make($this->whenLoaded('city')),
            // 'is_deliver' => $this->is_deliver,
            // 'zone_commission' => $this->zone_commission,
            // 'note' => $this->note
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
