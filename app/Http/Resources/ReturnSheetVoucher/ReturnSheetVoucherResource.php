<?php

namespace App\Http\Resources\ReturnSheetVoucher;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Gate\GateResource;
use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\Staff\StaffResource;
use App\Http\Resources\Pickup\PickupResource;
use App\Http\Resources\Parcel\ParcelCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\BusStation\BusStationResource;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Http\Resources\Merchant\MerchantCustomResource;
use App\Http\Resources\PaymentType\PaymentTypeResource;
use App\Http\Resources\StoreStatus\StoreStatusResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;

class ReturnSheetVoucherResource extends JsonResource
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
            'voucher_no' => $this->voucher_invoice,
            "pickup" => PickupResource::make($this->whenLoaded('pickup')),
            'receiver' => CustomerResource::make($this->customer),
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
            'payment_type' => PaymentTypeResource::make($this->payment_type),
            'parcels' => ParcelCollection::make($this->whenLoaded('parcels')),
            'delivery_counter' => $this->delivery_counter,
            'thirdparty_invoice' => $this->thirdparty_invoice,
            'total_item_price' => $this->total_item_price,
            'return_fee' => $this->return_fee,
            'return_type' => $this->return_type,
            'is_manual_return' => $this->is_manual_return,
            'total_delivery_amount' => $this->total_delivery_amount,
            'created_at' => $this->created_at->format('Y-m-d'),
            'return_sheet_voucher_note' => $this->when($this->return_sheet_vouchers, function () {
                return $this->return_sheet_vouchers->note;
            }),
            'return_sheet_voucher_priority' => $this->when($this->return_sheet_vouchers, function () {
                return $this->return_sheet_vouchers->priority;
            }),
            'return_sheet_voucher_created_at' => $this->when($this->return_sheet_vouchers, function () {
                return $this->return_sheet_vouchers->created_at;
            }),
            'assign_sheet' => $this->assignSheet(),
            'pending_returning_date'  => $this->pending_returning_date,
            'pending_returning_actor'  => $this->ReturningByResource(),
            'pending_returning_actor_type'  => $this->pending_returning_actor_type,
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

    protected function ReturningByResource()
    {
        if ($this->pending_returning_actor_type == "Merchant") {
            return MerchantCustomResource::make($this->whenLoaded('pending_returning_actor'));
        } else {
            return StaffResource::make($this->whenLoaded('pending_returning_actor'));
        } 
    }
}
