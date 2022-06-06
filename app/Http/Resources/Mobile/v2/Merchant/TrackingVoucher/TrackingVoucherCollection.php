<?php

namespace App\Http\Resources\Mobile\v2\Merchant\TrackingVoucher;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TrackingVoucherCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
	public $collects = TrackingVoucherResource::class;
    public function toArray($request)
    {
        return parent::toArray($request);
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
