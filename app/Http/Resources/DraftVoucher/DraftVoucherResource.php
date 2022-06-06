<?php

namespace App\Http\Resources\DraftVoucher;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Gate\GateResource;
use App\Http\Resources\Zone\ZoneResource;

use App\Http\Resources\Parcel\ParcelCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\BusStation\BusStationResource;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\PaymentType\PaymentTypeResource;
use App\Http\Resources\StoreStatus\StoreStatusResource;
use App\Http\Resources\PaymentStatus\PaymentStatusResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;


class DraftVoucherResource extends JsonResource
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
        if ($this->total_coupon_amount > 0) {
            $total_delivery_amount = $this->total_delivery_amount - $this->total_coupon_amount;
        } else {
            $total_delivery_amount = $this->discount_type == "extra" ?
                $this->total_delivery_amount + $this->total_discount_amount : $this->total_delivery_amount - $this->total_discount_amount;
        }

        $cbm = null;
        $weight = null;

        $insurance_fee  = getInsuranceFee();
        $bus_fee = 0;

        if ($this->payment_type_id == 6 || $this->payment_type_id == 8) {
            $bus_fee = $this->bus_fee;
        }

        return [
            'id' => $this->id,
            'receiver' => CustomerResource::make($this->whenLoaded('customer')),
            'voucher_no' => $this->voucher_invoice,
            'total_item_price' => $this->total_item_price,
            'total_delivery_amount' => $this->total_delivery_amount,
            'total_amount_to_collect' => $this->total_amount_to_collect,
            'total_discount_amount' => $this->total_discount_amount,
            'discount_type' => $this->discount_type,
            // 'total_coupon_amount' => number_format($this->total_coupon_amount),
            // 'total_bus_fee' => number_format($this->total_bus_fee),
            // 'transaction_fee' => number_format($this->transaction_fee),
            'total_coupon_amount' => $this->total_coupon_amount,
            'total_bus_fee' => $this->total_bus_fee,
            'transaction_fee' => $this->transaction_fee,
            'take_insurance' => ($this->insurance_fee > 0) ? 1 : 0,
            // 'insurance_fee' => number_format($this->insurance_fee),
            'insurance_fee' => $this->insurance_fee,
            'insurance_percentage' => $insurance_fee . '%',
            // 'warehousing_fee' => number_format($this->warehousing_fee),
            // 'delivery_commission' => number_format($this->delivery_commission),
            // 'grand_sub_total' => number_format($bus_fee + $total_delivery_amount + $this->transaction_fee + $this->insurance_fee + $this->warehousing_fee),
            'warehousing_fee' => $this->warehousing_fee,
            'delivery_commission' => $this->delivery_commission,
            'grand_sub_total' => $bus_fee + $total_delivery_amount + $this->transaction_fee + $this->insurance_fee + $this->warehousing_fee,
            'payment_type' => PaymentTypeResource::make($this->whenLoaded('payment_type')),
            'remark' => $this->remark,
            'sender_city' => CityResource::make($this->whenLoaded('sender_city')),
            'sender_zone' => ZoneResource::make($this->whenLoaded('sender_zone')),
            'receiver_city' => CityResource::make($this->whenLoaded('receiver_city')),
            'receiver_zone' => ZoneResource::make($this->whenLoaded('receiver_zone')),
            'bus_station' => $this->bus_station,
            'sender_bus_station' => BusStationResource::make($this->whenLoaded('sender_bus_station')),
            'receiver_bus_station' => BusStationResource::make($this->whenLoaded('receiver_bus_station')),
            'sender_gate' => GateResource::make($this->whenLoaded('sender_gate')),
            'receiver_gate' => GateResource::make($this->whenLoaded('receiver_gate')),
            // 'bus_credit' => $this->bus_credit,
            // 'deposit_amount' => $this->deposit_amount,
            'discount_id' => $this->discount_id,
            'coupon_id' => $this->coupon_id,
            'origin_city_id' => $this->origin_city_id,
            'call_status' => CallStatusResource::make($this->whenLoaded('call_status')),
            'delivery_status' => DeliveryStatusResource::make($this->whenLoaded('delivery_status')),
            'store_status' => StoreStatusResource::make($this->whenLoaded('store_status')),
            'payment_status' => PaymentStatusResource::make($this->whenLoaded('payment_status')),
            // 'postpone_date' => $this->postpone_date->format('m-d-Y g:i A'),
            'postpone_date' => $this->postpone_date ? date('Y-m-d', strtotime($this->postpone_date)) : $this->postpone_date,
            'parcels' => ParcelCollection::make($this->whenLoaded('parcels')),
            // 'amount_to_collect_sender' => number_format($this->sender_amount_to_collect),
            // 'amount_to_collect_receiver' => number_format($this->receiver_amount_to_collect),
            // 'return_fee' => number_format($this->return_fee),
            'amount_to_collect_sender' => $this->sender_amount_to_collect,
            'amount_to_collect_receiver' => $this->receiver_amount_to_collect,
            'return_fee' => $this->return_fee,
            'return_type' => $this->return_type,
            'is_closed' => $this->is_closed,
            'is_return' => $this->is_return,
            'is_manual_return' => $this->is_manual_return,
            'is_picked' => $this->is_picked,
            'is_bus_station_dropoff' => $this->is_bus_station_dropoff,
            'delegate_duration' => $this->delegate_duration_id,
            'delegate_person' => $this->delegate_person,
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A'),
            'delivered_date' =>  $this->delivered_date ?  $this->delivered_date->format('Y-m-d') : null,
            'transaction_date' =>  $this->transaction_date ?  $this->transaction_date->format('Y-m-d') : null,
            'deli_payment_status' => $this->deli_payment_status,
            'attachments' => AttachmentCollection::make($this->whenLoaded('attachments')),
            'thirdparty_invoice' => $this->thirdparty_invoice,
            'outgoing_status' => $this->outgoing_status,
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d') : null,
            'platform'  => $this->platform,
            'created_by_type'  => $this->created_by_type,
            'created_by_merchant' => MerchantResource::make($this->whenLoaded('created_by_merchant'))
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
