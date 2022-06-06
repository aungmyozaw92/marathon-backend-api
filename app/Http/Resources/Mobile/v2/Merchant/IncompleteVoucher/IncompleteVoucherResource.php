<?php

namespace App\Http\Resources\Mobile\v2\Merchant\IncompleteVoucher;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\v2\Merchant\Parcel\ParcelCollection;

class IncompleteVoucherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
	public function toArray($request)
	{
		$qr_code = ($this->qr_associate) ? $this->qr_associate->qr_code : '';
		return [
			'id' => $this->id,
			'qr_code' => $qr_code,
			'voucher_no' => $this->voucher_invoice,
			'created_at' => $this->created_at->format('Y-m-d'),
			'created_time' => $this->created_at->format('H:i A'),
			'parcels' => ParcelCollection::make($this->whenLoaded('parcels'))
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
