<?php

namespace App\Http\Resources\DashboardTracking;

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
            'created_time' => $this->created_at->format('H:i A'),
			'created_date' => date("jS F, Y", strtotime($this->created_at)),
			'status_id' => $this->tracking_status_id,
            'status_type' => $this->tracking_status->status,
            'status_name_mm' => $this->tracking_status->status_mm,
            'status_name_en' => $this->tracking_status->status_en,
            'from_city' => ($this->city) ? $this->city->name : null,
            'to_city' => $this->to_city()
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
        if ($this->tracking_status_id === 13 || $this->tracking_status_id === 14) {

            if ($this->voucher->return_from_waybill != null) {
                $waybill = $this->voucher->waybills()->where('from_city_id', $this->city_id)->latest()->first();
                if ($waybill) {
                    return $waybill->to_city->name;
                } else {
                    return null;
                }
            } else {
                return $this->voucher->receiver_city->name;
            }
        } else {
            return $this->city ? $this->city->name : null;
        }
    }
}
