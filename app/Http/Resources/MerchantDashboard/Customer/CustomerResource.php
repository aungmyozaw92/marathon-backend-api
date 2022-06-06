<?php

namespace App\Http\Resources\MerchantDashboard\Customer;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Zone\ZoneResource;
use Illuminate\Http\Resources\Json\JsonResource;

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
			'order' => $this->order,
            'success' => $this->success,
			'return' => $this->return,
			'city' => ($this->city) ? CityResource::make(($this->whenLoaded('city')))->only(['id','name','name_mm']) : null,
            'zone' =>($this->zone)? ZoneResource::make($this->whenLoaded('zone'))->only(['id','name','name_mm']): null,
			'address' => $this->address
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
