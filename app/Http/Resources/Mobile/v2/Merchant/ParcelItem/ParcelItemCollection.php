<?php

namespace App\Http\Resources\Mobile\v2\Merchant\ParcelItem;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Mobile\v2\Merchant\ParcelItem\ParcelItemResource;
class ParcelItemCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
	public $collects = ParcelItemResource::class;
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
