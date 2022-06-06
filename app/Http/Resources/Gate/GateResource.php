<?php

namespace App\Http\Resources\Gate;

use App\Http\Resources\Bus\BusResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BusStation\BusStationResource;

class GateResource extends JsonResource
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
            // 'bus_station_id' => $this->bus_station_id,
            // 'bus_id' => $this->bus_id,
            'gate_debit' => $this->gate_debit,
            'bus_station' => BusStationResource::make($this->whenLoaded('bus_station')),
            'bus' => BusResource::make($this->whenLoaded('bus'))
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
