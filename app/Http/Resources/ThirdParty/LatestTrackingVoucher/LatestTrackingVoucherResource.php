<?php

namespace App\Http\Resources\ThirdParty\LatestTrackingVoucher;

use App\Models\TrackingVoucher;
use Illuminate\Http\Resources\Json\JsonResource;

class LatestTrackingVoucherResource extends JsonResource
{
    private $condition = true;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $latest_tracking = TrackingVoucher::where('voucher_id', $this->id)->with(['tracking_status'])->latest()->first();
        return [
            'voucher_no' => $this->voucher_invoice,
            'last_tracking_status_en' => ($latest_tracking)? $latest_tracking->tracking_status->status_en : null,
            // 'last_tracking_status_mm' => ($latest_tracking)? $latest_tracking->tracking_status->status_mm : null
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
