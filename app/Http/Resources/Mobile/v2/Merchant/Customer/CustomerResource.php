<?php

namespace App\Http\Resources\Mobile\v2\Merchant\Customer;

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
			'name' => $this->name,
			'phone' => $this->phone,
			'other_phone' => $this->other_phone,
			'address' => $this->address,
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
