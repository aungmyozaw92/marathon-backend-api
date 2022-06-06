<?php

namespace App\Http\Resources\WayBillVoucher;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Gate\GateResource;
use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\Pickup\PickupResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Pickup\PickupCustomResource;
use App\Http\Resources\BusStation\BusStationResource;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Http\Resources\PaymentType\PaymentTypeResource;
use App\Http\Resources\StoreStatus\StoreStatusResource;
use App\Http\Resources\Customer\VoucherCustomerResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;

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
            $final_delivery_amount = $this->total_delivery_amount - $this->total_coupon_amount;
        } else {
            $final_delivery_amount = $this->discount_type == "extra" ?
                $this->total_delivery_amount + $this->total_discount_amount : $this->total_delivery_amount - $this->total_discount_amount;
        }
        $from_agent_fee = 0;
        $to_agent_fee = 0;
        if ($this->receiver_city->agent) {
            $to_agent_fee = $this->total_agent_fee;
        }
        if($this->sender_city->agent){
            $from_agent_fee = $this->total_agent_fee;
        }
        if($this->receiver_name){
            $customer = VoucherCustomerResource::make($this);
        }else{
            $customer = CustomerResource::make($this->whenLoaded('customer'));
        }
        return [
            'id' => $this->id,
            'voucher_no' => $this->voucher_invoice,
            'receiver_name' => $this->receiver_name,
            'receiver_phone' => $this->receiver_phone,
            'receiver_other_phone' => $this->receiver_other_phone,
            'receiver_address' => $this->receiver_address,
            "pickup" => PickupCustomResource::make($this->whenLoaded('pickup')),
            'receiver' => $customer,
            'receiver_city' => CityResource::make($this->whenLoaded('receiver_city')),
            'receiver_zone' => ZoneResource::make($this->whenLoaded('receiver_zone')),
            'receiver_bus_station' => BusStationResource::make($this->whenLoaded('receiver_bus_station')),
            'receiver_gate' => GateResource::make($this->whenLoaded('receiver_gate')),
            'sender_city' => CityResource::make($this->whenLoaded('sender_city')),
            'sender_zone' => ZoneResource::make($this->whenLoaded('sender_zone')),
            'sender_bus_station' => BusStationResource::make($this->whenLoaded('sender_bus_station')),
            'sender_gate' => GateResource::make($this->whenLoaded('sender_gate')),
            'bus_station' => $this->bus_station,
            // 'cbm' => $this->global_scale->cbm,GlobalScaleResource::make($this->whenLoaded('global_scale'))
            // 'max_weight' => $this->global_scale->max_weight,
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
            'final_delivery_amount' => $final_delivery_amount,
            'total_agent_fee' => $this->total_agent_fee,
            'from_agent_fee' => $from_agent_fee,
            'to_agent_fee' => $to_agent_fee,
            'merchant_payment_status' => $this->merchant_payment_status,
            'agent_payment_status' => $this->agent_payment_status,
            'waybill_voucher_note' => $this->when($this->waybill_vouchers, function () {
                return $this->waybill_vouchers->note;
            }),
            'waybill_voucher_priority' => $this->when($this->waybill_vouchers, function () {
                return $this->waybill_vouchers->priority;
            }),
            'waybill_voucher_created_at' => $this->when($this->waybill_vouchers, function () {
                return $this->waybill_vouchers->created_at;
            }),
            'thirdparty_invoice' => $this->thirdparty_invoice,
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A'),
            'transaction_date' =>  $this->transaction_date ?  $this->transaction_date->format('Y-m-d') : null,
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
