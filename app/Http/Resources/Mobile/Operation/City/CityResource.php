<?php

namespace App\Http\Resources\Mobile\Operation\City;

use Illuminate\Http\Resources\Json\JsonResource;

//use App\Http\Resources\Mobile\Delivery\Zone\ZoneCollection;

class CityResource extends JsonResource
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
            'is_collect_only' => $this->is_collect_only,
            'is_on_demand' => $this->is_on_demand,
            'is_available_d2d' => $this->is_available_d2d,
            'is_available_ecom' => $this->is_available_ecom,
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
