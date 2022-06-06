<?php

namespace App\Http\Resources\Zone;

use App\Http\Resources\City\CityResource;
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
            'zone_rate' => $this->zone_rate,
            'diff_zone_rate' => $this->diff_zone_rate,
            'zone_agent_rate' => $this->zone_agent_rate,
            'city' => CityResource::make($this->whenLoaded('city')),
            'is_deliver' => $this->is_deliver,
            'is_available_ecom' => $this->is_available_ecom,
            'zone_commission' => $this->zone_commission,
            'outsource_rate'  => $this->outsource_rate,
            'note' => $this->note,
            'outsource_car_rate' => $this->outsource_car_rate
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
