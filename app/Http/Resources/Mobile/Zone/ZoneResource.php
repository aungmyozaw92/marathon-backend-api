<?php

namespace App\Http\Resources\Mobile\Zone;

use Rabbit;
use App\Http\Resources\Mobile\City\CityResource;
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
            'city_id' => $this->city_id,
            'is_deliver' => $this->is_deliver,
            'note' => $this->note,
            'zone_commission' => $this->zone_commission,
            'is_available_ecom' => $this->is_available_ecom,
            'outsource_rate'  => $this->outsource_rate,
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
