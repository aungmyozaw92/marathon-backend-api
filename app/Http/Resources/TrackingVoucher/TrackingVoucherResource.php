<?php

namespace App\Http\Resources\TrackingVoucher;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Voucher\VoucherResource;
use App\Http\Resources\TrackingStatus\TrackingStatusResource;
use App\Http\Resources\City\CityResource;

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
            'id' => $this->id,
            'tracking_status_id' => $this->tracking_status_id,
            'city_id' => $this->city_id,
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A'),
            'tracking_status' => TrackingStatusResource::make($this->whenLoaded('tracking_status')),
            'from_city' => ($this->city) ? $this->city->name : null,
            // 'to_city' => $this->voucher->origin_city_id !== $this->voucher->receiver_city ? $this->voucher->sender_city->name : $this->voucher->origin_city->name,
            // 'to_city' => $this->city_id === $this->voucher->receiver_city_id ? $this->city->name : $this->voucher->sender_city->name,
            'to_city' => $this->tracking_status_id === 3 ? $this->voucher->receiver_city->name : ($this->tracking_status_id === 6 ? $this->voucher->sender_city->name : ( $this->city)? $this->city->name :null)
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
