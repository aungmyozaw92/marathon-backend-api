<?php

namespace App\Http\Resources\Mobile\Delivery\BusSheetVoucher;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\Pickup\PickupResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BusSheet\BusSheetResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Http\Resources\PaymentType\PaymentTypeResource;
use App\Http\Resources\StoreStatus\StoreStatusResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;
use App\Http\Resources\Mobile\Delivery\Voucher\VoucherResource;
use App\Http\Resources\Mobile\Delivery\DeliSheet\DeliSheetResource;

class BusSheetVoucherResource extends JsonResource
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
            'bus_sheet' => BusSheetResource::make($this->whenLoaded('bus_sheet')),
            'voucher' => VoucherResource::make($this->whenLoaded('voucher')),
            'actual_bus_fee' => $this->actual_bus_fee,
            'delivery_status_id' => $this->delivery_status_id,
            'is_return' => $this->is_return,
            'is_paid' => $this->is_paid,
            'note' => $this->note,
            // 'note' => getConvertedUni2Zg($this->note),
            'priority' => $this->priority,
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
