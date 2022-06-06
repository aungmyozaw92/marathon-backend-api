<?php

namespace App\Http\Resources\Mobile\v2\Merchant\Pickup;

use Illuminate\Http\Resources\Json\JsonResource;
class PickupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
		$total_vouchers =  $this->vouchers()->count();
		$finished_vouchers =  $this->vouchers()->where('is_closed', 1)->count();
        return [
			'id' => $this->id,
			'pickup_invoice' => $this->pickup_invoice,
			'qty' => $this->qty,
			'note' => $this->note,
			'branch_id' => $this->sender_associate_id,
			'requested_date' =>  $this->requested_date ?  $this->requested_date->format('Y-m-d') : null,
			'pickup_date' => $this->pickup_date,
			'is_closed' => $this->is_closed,
			'is_pickuped' => $this->is_pickuped,
			'is_finished_all' => $total_vouchers == $finished_vouchers?true:false,
			'created_at' => $this->created_at->format('Y-m-d'),
			'created_time' => $this->created_at->format('H:i A')
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
