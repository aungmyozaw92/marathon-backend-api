<?php

namespace App\Http\Resources\Mobile\Calculator\Zone;

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
            'name_mm' => getConvertedUni2Zg($this->name_mm),
            'zone_rate' => $this->zone_rate,
            'zone_agent_rate' => $this->zone_agent_rate,
            'city_id' => $this->city_id,
            'is_deliver' => $this->is_deliver,
            'note' => $this->note,
            'zone_commission' => $this->zone_commission,
            'outsource_rate'  => $this->outsource_rate,
            // 'note' => getConvertedUni2Zg($this->note)
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
