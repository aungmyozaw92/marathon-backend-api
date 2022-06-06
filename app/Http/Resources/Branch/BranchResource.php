<?php

namespace App\Http\Resources\Branch;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\Account\AccountResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'account' => AccountResource::make($this->whenLoaded('account')),
            'city' => CityResource::make($this->whenLoaded('city')),
            'zone' => ZoneResource::make($this->whenLoaded('zone')),
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
