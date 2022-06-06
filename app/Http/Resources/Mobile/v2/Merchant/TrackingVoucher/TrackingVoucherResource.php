<?php

namespace App\Http\Resources\Mobile\v2\Merchant\TrackingVoucher;

use Illuminate\Http\Resources\Json\JsonResource;

class TrackingVoucherResource extends JsonResource
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
			'voucher_invoice' => $this->voucher->voucher_invoice,
			'status_en' => $this->tracking_status->status,
			'status_mm' => $this->tracking_status->status_mm,
			'from_city_en' => $this->city ? $this->city->name : null,
			'from_city_mm' => $this->city ? $this->city->name_mm : null,
			'to_city_en' => $this->to_city()['name'],
			'to_city_mm' => $this->to_city()['name_mm'],
			// 'created_at' => $this->created_at->format('Y-m-d'),
			'created_at' => date("jS F, Y", strtotime($this->created_at)),
			'created_time' => $this->created_at->format('g:i A'),
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

	public function to_city()
	{
		if ($this->tracking_status_id === 14) {
			if ($this->voucher->return_from_waybill != null) {
				$waybill = $this->voucher->waybills()->where('from_city_id', $this->city_id)->latest()->first();
				if ($waybill) {
					return ['name'=>$waybill->to_city->name,'name_mm'=>$waybill->to_city->name_mm];
				} else {
					return ['name'=>null,'name_mm'=>null];
				}
			} else {
				return ['name'=>$this->voucher->receiver_city->name,'name_mm'=>$this->voucher->receiver_city->name_mm];
			}
		} else {
			return $this->city ? ['name'=>$this->city->name,'name_mm'=>$this->city->name] : ['name' => null, 'name_mm' => null];;
		}
	}
}
