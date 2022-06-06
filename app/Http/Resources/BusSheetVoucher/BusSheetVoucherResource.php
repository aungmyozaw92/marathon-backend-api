<?php

namespace App\Http\Resources\BusSheetVoucher;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Gate\GateResource;
use App\Http\Resources\Pickup\PickupResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\BusStation\BusStationResource;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Http\Resources\PaymentType\PaymentTypeResource;
use App\Http\Resources\StoreStatus\StoreStatusResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;
use App\Http\Resources\MerchantAssociate\MerchantAssociateResource;
use App\Http\Resources\Attachment\AttachmentCollection;

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
            'voucher_no' => $this->voucher_invoice,
            // "pickup" => PickupResource::make($this->whenLoaded('pickup')),
            'sender_type' => $this->when($this->pickup, $this->pickup->sender_type),
            'sender' => $this->senderResource(),
            'sender_associate' => $this->when($this->pickup->sender_associate_id, MerchantAssociateResource::make($this->pickup->sender_associate)),
            'receiver' => CustomerResource::make($this->whenLoaded('customer')),
            'receiver_city' => CityResource::make($this->whenLoaded('receiver_city')),
            'receiver_zone' => ZoneResource::make($this->whenLoaded('receiver_zone')),
            'receiver_bus_station' => BusStationResource::make($this->whenLoaded('receiver_bus_station')),
            'sender_bus_station' => BusStationResource::make($this->whenLoaded('sender_bus_station')),
            'receiver_gate' => GateResource::make($this->whenLoaded('receiver_gate')),
            'sender_gate' => GateResource::make($this->whenLoaded('sender_gate')),
            // 'bus_fee' => number_format($this->total_bus_fee),
            'bus_fee' => $this->total_bus_fee,
            // 'actual_bus_fee' => $this->when($this->bus_sheet_vouchers, function () {
            //     return number_format($this->bus_sheet_vouchers->actual_bus_fee);
            // }),
            'actual_bus_fee' => $this->when($this->bus_sheet_vouchers, function () {
                return $this->bus_sheet_vouchers->actual_bus_fee;
            }),
            'is_return' => $this->when($this->bus_sheet_vouchers, function () {
                return $this->bus_sheet_vouchers->is_return;
            }),
            // 'cbm' => $this->global_scale->cbm,GlobalScaleResource::make($this->whenLoaded('global_scale'))
            // 'max_weight' => $this->global_scale->max_weight,
            'call_status' => CallStatusResource::make($this->whenLoaded('call_status')),
            'delivery_status' => DeliveryStatusResource::make($this->whenLoaded('delivery_status')),
            'store_status' => StoreStatusResource::make($this->whenLoaded('store_status')),
            // 'amount_to_collect_sender' => number_format($this->sender_amount_to_collect),
            // 'amount_to_collect_receiver' => number_format($this->receiver_amount_to_collect),
            'amount_to_collect_sender' => $this->sender_amount_to_collect,
            'amount_to_collect_receiver' => $this->receiver_amount_to_collect,
            'payment_type' => PaymentTypeResource::make($this->whenLoaded('payment_type')),
            'note' => $this->remark,
            'delivered_date' =>  $this->delivered_date ?  $this->delivered_date->format('Y-m-d') : null,
            'deli_payment_status' => $this->deli_payment_status,
            'bus_sheet_voucher_note' => $this->when($this->bus_sheet_vouchers, function () {
                return $this->bus_sheet_vouchers->note;
            }),
            'bus_sheet_voucher_priority' => $this->when($this->bus_sheet_vouchers, function () {
                return $this->bus_sheet_vouchers->priority;
            }),
            'attachments' => AttachmentCollection::make($this->whenLoaded('attachments')),
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

    protected function senderResource()
    {
        if ($this->pickup) {
            if ($this->pickup->sender_type == "Merchant") {
                return MerchantResource::make($this->pickup->sender);
            } elseif ($this->pickup->sender_type == "Customer") {
                return CustomerResource::make($this->pickup->sender);
            }
        }
    }
}
