<?php

namespace App\Http\Resources\BusStation;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Gate\GateCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Zone\ZoneResource;

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
            'name' => $this->name,
            // 'lat' => $this->lat,
            // 'long' => $this->long,
            'number_of_gates' => $this->number_of_gates,
            // 'delivery_rate' => number_format($this->delivery_rate),
            'delivery_rate' => $this->delivery_rate,
            'city' => CityResource::make($this->whenLoaded('city')),
            'zone' => ZoneResource::make($this->whenLoaded('zone')),
             'gates' => GateCollection::make($this->whenLoaded('gates')),
            // 'route_cities' => $this->route_cities->pluck('destination_id')
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
