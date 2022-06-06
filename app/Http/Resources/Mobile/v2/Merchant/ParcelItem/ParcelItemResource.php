<?php

namespace App\Http\Resources\Mobile\v2\Merchant\ParcelItem;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\v2\Merchant\Product\ProductResource;
class ParcelItemResource extends JsonResource
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
			'id' => $this->id,
			'parcel_id' => $this->parcel_id,
			'item_name' => $this->item_name,
			'item_qty' => $this->item_qty,
			// 'item_price' => number_format($this->item_price),
			'item_price' => $this->item_price,
			'item_status' => $this->item_status,
			'weight' => $this->weight,
			'lwh' => $this->lwh,
			'product'         => ProductResource::make($this->whenLoaded('product')),
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
