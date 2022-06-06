<?php

namespace App\Http\Resources\SuperMerchant\TrackingVoucher;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TrackingStatus\TrackingStatusResource;


class TrackingVoucherResource extends JsonResource
{
    private $condition = true;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    private $tracking_vouchers;
    public function toArray($request)
    {   
        return [
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A'),
            'status_type' => $this->tracking_status->status,
            'status_name_mm' => $this->tracking_status->status_mm,
            'status_name_en' => $this->tracking_status->status_en,
            'from_city' => ($this->city) ? $this->city->name : null,
            // 'to_city' => $this->voucher->origin_city_id !== $this->voucher->receiver_city ? $this->voucher->sender_city->name : $this->voucher->origin_city->name,
            // 'to_city' => $this->city_id === $this->voucher->receiver_city_id ? $this->city->name : $this->voucher->sender_city->name,
            'to_city' => $this->tracking_status_id === 3 ? $this->voucher->receiver_city->name : ($this->tracking_status_id === 6 ? $this->voucher->sender_city->name : ( $this->city)? $this->city->name :null)
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
