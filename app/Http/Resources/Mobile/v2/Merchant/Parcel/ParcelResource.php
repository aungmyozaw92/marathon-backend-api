<?php

namespace App\Http\Resources\Mobile\v2\Merchant\Parcel;

use App\Http\Resources\Mobile\GlobalScale\GlobalScaleResource;
use App\Http\Resources\Mobile\v2\Merchant\ParcelItem\ParcelItemCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ParcelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
		// return parent::toArray($request);
		return [
			'id'                   => $this->id,
			'weight'               => $this->weight,
			'global_scale'         => GlobalScaleResource::make($this->global_scale),
			'parcel_items'         => ParcelItemCollection::make($this->whenLoaded('parcel_items')),
		];
    }
	/**
	 * Get additional data that should be returned with the resource array.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return array
	 */
	public function with($request)
	{
		return [
			'status' => 1,
		];
	}
}
