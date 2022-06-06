<?php

namespace App\Http\Resources\MerchantDiscount;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\DiscountType\DiscountTypeResource;

class MerchantDiscountResource extends JsonResource
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
            'id' => $this->id,
            'amount' => $this->amount,
            // 'merchant_id' => MerchantResource::make($this->merchant),
            'merchant_id' => $this->merchant_id,
            'discount_type' => DiscountTypeResource::make($this->discount_type),
            'normal_or_dropoff' => $this->normal_or_dropoff,
            'extra_or_discount' => $this->extra_or_discount,
            'sender_city_id' => $this->sender_city_id,
            'receiver_city_id' => $this->receiver_city_id,
            'sender_zone_id' => $this->sender_zone_id,
            'receiver_zone_id' => $this->receiver_zone_id,
            'from_bus_station_id' => $this->from_bus_station_id,
            'to_bus_station_id' => $this->to_bus_station_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'note' => $this->note,
            'platform' => $this->platform
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
