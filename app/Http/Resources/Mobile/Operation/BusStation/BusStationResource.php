<?php

namespace App\Http\Resources\Mobile\Operation\BusStation;

use Illuminate\Http\Resources\Json\JsonResource;

class BusStationResource extends JsonResource
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
            // 'name' => getConvertedUni2Zg($this->name),
            'name' => $this->name,
            'number_of_gates' => $this->number_of_gates,
            'delivery_rate' => $this->delivery_rate,
            'city_id' => $this->city_id,
            'zone_id' => $this->zone_id,
            'route_cities' => $this->route_cities->pluck('destination_id')
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
