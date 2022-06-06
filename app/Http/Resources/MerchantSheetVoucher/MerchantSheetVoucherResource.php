<?php

namespace App\Http\Resources\MerchantSheetVoucher;

use App\Models\Pickup;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Gate\GateResource;
use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\Pickup\PickupResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\BusStation\BusStationResource;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Http\Resources\PaymentType\PaymentTypeResource;
use App\Http\Resources\StoreStatus\StoreStatusResource;
use App\Http\Resources\Customer\VoucherCustomerResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;

class MerchantSheetVoucherResource extends JsonResource
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
        $account_id = Pickup::find($this->pickup_id)->sender->account->id;
        $debit_amount = $this->journals()->debitAmount($this->id, $account_id);

        $credit_amount = $this->journals()->creditAmount($this->id, $account_id);

        if($this->receiver_name){
            $customer = VoucherCustomerResource::make($this);
        }else{
            $customer = CustomerResource::make($this->whenLoaded('customer'));
        }
        return [
            'id' => $this->id,
            'voucher_no' => $this->voucher_invoice,
            "pickup" => PickupResource::make($this->whenLoaded('pickup')),
            'receiver' => $customer,
            'receiver_name' => $this->receiver_name,
            'receiver_phone' => $this->receiver_phone,
            'receiver_other_phone' => $this->receiver_other_phone,
            'receiver_address' => $this->receiver_address,
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
            'store_status' => StoreStatusResource::make($this->whenLoaded('store_status')),
            'amount_to_collect_sender' => $this->sender_amount_to_collect,
            'amount_to_collect_receiver' => $this->receiver_amount_to_collect,
            'total_item_price' => $this->total_item_price,
            'total_bus_fee' => $this->total_bus_fee,
            'total_delivery_amount' => $this->total_delivery_amount,
            'delivery_commission' => $this->delivery_commission,
            'debit_amount' => $debit_amount,
            'credit_amount' => $credit_amount,
            'balance' => $debit_amount - $credit_amount,
            'payment_type' => PaymentTypeResource::make($this->whenLoaded('payment_type')),
            'note' => $this->remark,
            'created_at' => $this->created_at->format('Y-m-d'),
            'delivered_date' =>  $this->delivered_date ?  $this->delivered_date->format('Y-m-d') : null,
            'deli_payment_status' => $this->deli_payment_status,
            'assign_sheet' => $this->assignSheet(),
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

    protected function assignSheet()
    {
        if ($this->outgoing_status === 0) {
            return $this->delisheets()->latest()->first();
        } elseif ($this->outgoing_status === 1) {
            return $this->waybills()->latest()->first();
        } elseif ($this->outgoing_status === 2) {
            return $this->bussheets()->latest()->first();
        } elseif ($this->outgoing_status === 3) {
            return "Merchant Sheet Draft";
        } elseif ($this->outgoing_status === 4) {
            return $this->merchant_sheets()->latest()->first();
        } elseif ($this->outgoing_status === 5) {
            return $this->return_sheets()->latest()->first();
        }
    }
}
