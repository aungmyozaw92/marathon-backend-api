<?php

namespace App\Http\Resources\Route;

use App\Http\Resources\City\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource
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
            'travel_day' => $this->travel_day,
            //'route_rate' => $this->route_rate,
            'route_name' => $this->route_name,
            //'route_agent_rate' => $this->route_agent_rate,
            'origin_city' => CityResource::make($this->whenLoaded('origin_city')),
            'destination_city' => CityResource::make($this->whenLoaded('destination_city'))
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
