<?php

namespace App\Http\Resources\Mobile\Delivery\DeliSheetVoucher;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\Pickup\PickupResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Http\Resources\PaymentType\PaymentTypeResource;
use App\Http\Resources\StoreStatus\StoreStatusResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;
use App\Http\Resources\Mobile\Delivery\Voucher\VoucherResource;
use App\Http\Resources\Mobile\Delivery\DeliSheet\DeliSheetResource;

class DeliSheetVoucherResource extends JsonResource
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
            'deli_sheet' => DeliSheetResource::make($this->whenLoaded('deli_sheet')),
            'return' => $this->return,
            'ATC_receiver' => $this->ATC_receiver,
            'note' => getConvertedUni2Zg($this->note),
            'priority' => $this->priority,
            'delivery_status' => DeliveryStatusResource::make($this->whenLoaded('delivery_status')),
            'voucher' => VoucherResource::make($this->whenLoaded('voucher')),
            'payment_type' => PaymentTypeResource::make($this->whenLoaded('payment_type')),
            'receiver' => CustomerResource::make($this->whenLoaded('customer')),
            'delivery_commission' => $this->delivery_commission

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
