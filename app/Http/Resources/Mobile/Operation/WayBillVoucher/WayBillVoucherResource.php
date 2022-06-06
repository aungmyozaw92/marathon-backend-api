<?php

namespace App\Http\Resources\Mobile\Operation\WayBillVoucher;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Http\Resources\StoreStatus\StoreStatusResource;
use App\Http\Resources\Mobile\Customer\CustomerResource;
use App\Http\Resources\Mobile\Operation\City\CityResource;
use App\Http\Resources\Mobile\Operation\Gate\GateResource;
use App\Http\Resources\Mobile\Operation\Zone\ZoneResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;
use App\Http\Resources\Mobile\GlobalScale\GlobalScaleResource;
use App\Http\Resources\Mobile\Operation\Pickup\PickupResource;
use App\Http\Resources\Mobile\PaymentType\PaymentTypeResource;
use App\Http\Resources\Mobile\Operation\BusStation\BusStationResource;

class WayBillVoucherResource extends JsonResource
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
        if ($this->total_coupon_amount > 0) {
            $total_discount_amount = 0 - $this->total_coupon_amount;
        } else {
            $total_discount_amount = $this->discount_type == "extra" ?
                                                    $this->total_discount_amount : 0 - $this->total_discount_amount;
        }

        return [
            'id' => $this->id,
            'voucher_no' => $this->voucher_invoice,
            "pickup" => PickupResource::make($this->whenLoaded('pickup')),
            'receiver' => CustomerResource::make($this->whenLoaded('customer')),
            'receiver_city' => CityResource::make($this->whenLoaded('receiver_city')),
            'receiver_zone' => ZoneResource::make($this->whenLoaded('receiver_zone')),
            'receiver_bus_station' => BusStationResource::make($this->whenLoaded('receiver_bus_station')),
            'receiver_gate' => GateResource::make($this->whenLoaded('receiver_gate')),
            'sender_city' => CityResource::make($this->whenLoaded('sender_city')),
            'sender_zone' => ZoneResource::make($this->whenLoaded('sender_zone')),
            'sender_bus_station' => BusStationResource::make($this->whenLoaded('sender_bus_station')),
            'sender_gate' => GateResource::make($this->whenLoaded('sender_gate')),
            'bus_station' => $this->bus_station,
            'call_status' => CallStatusResource::make($this->whenLoaded('call_status')),
            'delivery_status' => DeliveryStatusResource::make($this->whenLoaded('delivery_status')),
            'delivery_status_id' => $this->delivery_status_id,
            'store_status' => StoreStatusResource::make($this->whenLoaded('store_status')),
            'amount_to_collect_sender' => $this->sender_amount_to_collect,
            'amount_to_collect_receiver' => $this->receiver_amount_to_collect,
            'total_item_price' => $this->total_item_price,
            'payment_type' => PaymentTypeResource::make($this->whenLoaded('payment_type')),
            'note' => $this->remark,
            'is_closed' => $this->is_closed,
            'total_delivery_amount' => $this->total_delivery_amount,
            'total_agent_fee' => $this->total_agent_fee,
            'total_discount_amount' => $total_discount_amount,
            'merchant_payment_status' => $this->merchant_payment_status,
            'agent_payment_status' => $this->agent_payment_status,
            'waybill_voucher_note' => $this->when($this->waybill_vouchers, function () {
                return $this->waybill_vouchers->note;
            }),
            'waybill_voucher_priority' => $this->when($this->waybill_vouchers, function () {
                return $this->waybill_vouchers->priority;
            }),
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A'),
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
