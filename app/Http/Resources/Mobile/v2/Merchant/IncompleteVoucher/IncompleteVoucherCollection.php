<?php

namespace App\Http\Resources\Mobile\v2\Merchant\IncompleteVoucher;

use Illuminate\Http\Resources\Json\ResourceCollection;

class IncompleteVoucherCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
	public $collects = IncompleteVoucherResource::class;
    public function toArray($request)
    {
        return parent::toArray($request);
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
