<?php

namespace App\Http\Resources\Mobile\Operation\Voucher;

use App\Models\City;
use App\Models\Zone;
use App\Models\Pickup;
use App\Models\Merchant;
use App\Models\DiscountType;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Gate\GateResource;
use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\Parcel\ParcelResource;
use App\Http\Resources\Pickup\PickupResource;
use App\Http\Resources\Parcel\ParcelCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\BusStation\BusStationResource;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Http\Resources\PaymentType\PaymentTypeResource;
use App\Http\Resources\StoreStatus\StoreStatusResource;
use App\Http\Resources\PaymentStatus\PaymentStatusResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;

class VoucherResource extends JsonResource
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
            $total_discount_amount = 0 - $this->total_coupon_amount;
        } else {
            $total_discount_amount = $this->discount_type == "extra" ?
                                            $this->total_discount_amount : 0 - $this->total_discount_amount;
        }

        $discount_amount = ($this->total_coupon_amount > 0) ? $this->total_coupon_amount : $this->total_discount_amount;
        // $pickup = Pickup::withTrashed()->where('id', 1)->first();
        $parcels = ParcelCollection::make($this->whenLoaded('parcels'));
        $discount_details = [];
        $target_date_between = getVolumnTargetDateBetween();
        if ($target_date_between) {
            $discount_details['target_sale_count'] = (int)getTargetSaleCount();
            $discount_details['target_coupon']   = (int)getTargetCoupon();
        }
        
        $pickup = Pickup::find($this->pickup_id);
        if ($pickup->sender_type === 'Merchant') {
            $merchant = Merchant::find($pickup->sender_id);

            $discount_details['current_sale_count'] = $merchant->current_sale_count;
            $discount_details['available_coupon']   = $merchant->available_coupon;
        }
        
        if ($this->parcels[0]['discount_type_id']) {
            $discount_type = DiscountType::find($this->parcels[0]['discount_type_id']);
            $discount_details['discount_name']   = $discount_type->name;
            $discount_details['discount_amount']   = $this->parcels[0]['discount_amount'];
            $discount_details['parcel_count']   = $this->parcels->count();
            $discount_details['discount_type'] = $this->discount_type;
        }
        $insurance_fee  = getInsuranceFee();
        $bus_fee = 0;

        if ($this->payment_type_id == 6 || $this->payment_type_id == 8) {
            $bus_fee = $this->bus_fee;
        }
        $qr_code = ($this->qr_associate) ? $this->qr_associate->qr_code : '';
        return [
            'id' => $this->id,
            'qr_code' => $qr_code,
            'receiver' => CustomerResource::make($this->whenLoaded('customer')),
           // "pickup" => PickupResource::make($this->whenLoaded('pickup')),
            'discount_details' => $discount_details,
            'pickup_id' => $this->pickup_id,
            'voucher_no' => $this->voucher_invoice,
            'total_item_price' => $this->total_item_price,
            'total_delivery_amount' => $this->total_delivery_amount,
            'total_amount_to_collect' => $this->total_amount_to_collect,
            'total_discount_amount' => $total_discount_amount,
            'total_coupon_amount' => $this->total_coupon_amount,
            'total_bus_fee' => $this->total_bus_fee,
            'transaction_fee' => $this->transaction_fee,
            'take_insurance' => ($this->insurance_fee > 0) ? 1 : 0,
            'insurance_fee' => $this->insurance_fee,
            'insurance_percentage' => $insurance_fee . '%',
            'warehousing_fee' => $this->warehousing_fee,
            'grand_sub_total' => $bus_fee + ($this->total_delivery_amount - $discount_amount) + $this->transaction_fee + $this->insurance_fee + $this->warehousing_fee,
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
            'parcels' => $parcels,
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
            'thirdparty_invoice' => $this->thirdparty_invoice
            //'journals' => Journal::debit($this)->get(),
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
