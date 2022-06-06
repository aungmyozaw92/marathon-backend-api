<?php

namespace App\Http\Resources\DeliSheetVoucher;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\Pickup\PickupResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Pickup\PickupCustomResource;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\PaymentType\PaymentTypeResource;
use App\Http\Resources\StoreStatus\StoreStatusResource;
use App\Http\Resources\Customer\VoucherCustomerResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;

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
        if($this->receiver_name){
            $customer = VoucherCustomerResource::make($this);
        }else{
            $customer = CustomerResource::make($this->whenLoaded('customer'));
        }
        $delivery_fee = ($this->discount_type == 'extra') ? 
                             $this->total_delivery_amount + $this->total_discount_amount : $this->total_delivery_amount - $this->total_discount_amount;
        return [
            'id' => $this->id,
            'voucher_no' => $this->voucher_invoice,
            "pickup" => PickupCustomResource::make($this->whenLoaded('pickup')),
            'receiver' => $customer,
            'receiver_name' => $this->receiver_name,
            'receiver_phone' => $this->receiver_phone,
            'receiver_other_phone' => $this->receiver_other_phone,
            'receiver_address' => $this->receiver_address,
            'receiver_city' => CityResource::make($this->whenLoaded('receiver_city')),
            'receiver_zone' => ZoneResource::make($this->whenLoaded('receiver_zone')),
            // 'cbm' => $this->global_scale->cbm,GlobalScaleResource::make($this->whenLoaded('global_scale'))
            // 'max_weight' => $this->global_scale->max_weight,
            'total_delivery_amount' => $delivery_fee,
            'delivery_commission' => $this->delivery_commission,
            'total_item_price' => $this->total_item_price,
            'total_agent_fee' => $this->total_agent_fee,
            'call_status' => CallStatusResource::make($this->whenLoaded('call_status')),
            'delivery_status' => DeliveryStatusResource::make($this->whenLoaded('delivery_status')),
            'store_status' => StoreStatusResource::make($this->whenLoaded('store_status')),
            'amount_to_collect_sender' => $this->sender_amount_to_collect,
            'amount_to_collect_receiver' => $this->receiver_amount_to_collect,
            'merchant_payment_status' => $this->merchant_payment_status,
            'agent_payment_status' => $this->agent_payment_status,
            'payment_type' => PaymentTypeResource::make($this->whenLoaded('payment_type')),
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A'),
            'transaction_date' =>  $this->transaction_date ?  $this->transaction_date->format('Y-m-d') : null,
            'postpone_date' => $this->postpone_date ? $this->postpone_date->format('Y-m-d') : null,
            'delivered_date' => $this->delivered_date ?  $this->delivered_date->format('Y-m-d') : null,
            'deli_payment_status' => $this->deli_payment_status,
            'thirdparty_invoice' => $this->thirdparty_invoice,
            'delivery_counter' => $this->delivery_counter,
            'note' => $this->remark,
            'deli_sheet_voucher_note' => $this->when($this->deli_sheet_vouchers, function () {
                return $this->deli_sheet_vouchers->note;
            }),
            'deli_sheet_voucher_priority' => $this->when($this->deli_sheet_vouchers, function () {
                return $this->deli_sheet_vouchers->priority;
            }),
            'deli_sheet_voucher_delivery_status' => $this->when($this->deli_sheet_vouchers, function () {
                return $this->deli_sheet_vouchers->delivery_status;
            }),
            'deli_sheet_voucher_is_came_from_mobile' => $this->when($this->deli_sheet_vouchers, function () {
                return $this->deli_sheet_vouchers->is_came_from_mobile;
            }),
            'deli_sheet_voucher_return' => $this->when($this->deli_sheet_vouchers, function () {
                return $this->deli_sheet_vouchers->return;
            }),
            'deli_sheet_voucher_created_at' => $this->when($this->deli_sheet_vouchers, function () {
                return $this->deli_sheet_vouchers->created_at;
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
}
