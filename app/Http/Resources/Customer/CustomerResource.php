<?php

namespace App\Http\Resources\Customer;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Badge\BadgeResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Zone\ZoneResource;

class CustomerResource extends JsonResource
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
            'phone' => $this->phone,
            'other_phone' => $this->other_phone,
            'address' => $this->address,
            'point' => $this->point,
            'order' => $this->order,
            'success' => $this->success,
            'return' => $this->return,
            'rate' => $this->rate,
            'lat' => $this->lat,
            'long' => $this->long,
            'city' => CityResource::make($this->whenLoaded('city')),
            'zone' => ZoneResource::make($this->whenLoaded('zone')),
            'badge' => BadgeResource::make($this->whenLoaded('badge'))
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
