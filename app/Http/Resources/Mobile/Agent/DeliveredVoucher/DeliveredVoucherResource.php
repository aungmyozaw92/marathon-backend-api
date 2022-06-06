<?php

namespace App\Http\Resources\Mobile\Agent\DeliveredVoucher;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Zone\ZoneResource;


class DeliveredVoucherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'voucher_no' => $this->voucher_invoice,
            
            'receiver' => CustomerResource::make($this->whenLoaded('customer')),
            
            'receiver_zone' => ZoneResource::make($this->whenLoaded('receiver_zone')),
            
            'amount_to_collect_receiver' => $this->receiver_amount_to_collect,
            
            'delivered_date' =>  $this->delivered_date ?  $this->delivered_date->format('Y-m-d') : null,
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
