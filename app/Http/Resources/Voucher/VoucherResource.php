<?php

namespace App\Http\Resources\Voucher;

use App\Models\City;
use App\Models\Zone;
use App\Models\Pickup;
use App\Models\Journal;
use App\Models\Voucher;
use App\Models\Merchant;
use App\Models\DiscountType;
use App\Models\TrackingVoucher;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Gate\GateResource;
use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\Agent\AgentResource;
use App\Http\Resources\Staff\StaffResource;
use App\Http\Resources\Parcel\ParcelResource;
use App\Http\Resources\Pickup\PickupResource;
use App\Http\Resources\Parcel\ParcelCollection;
use App\Repositories\Web\Api\v1\CityRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\BusStation\BusStationResource;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\Merchant\MerchantCustomResource;
use App\Http\Resources\PaymentType\PaymentTypeResource;
use App\Http\Resources\StoreStatus\StoreStatusResource;
use App\Http\Resources\Customer\VoucherCustomerResource;
use App\Http\Resources\PaymentStatus\PaymentStatusResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;
use App\Http\Resources\TrackingVoucher\TrackingVoucherResource;
use App\Http\Resources\DelegateDuration\DelegateDurationResource;
use App\Http\Resources\TrackingVoucher\TrackingVoucherCollection;

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

    private $tracking_vouchers;
    public function toArray($request)
    {
        $this->condition = !str_contains($request->route()->uri(), 'merchant_sheets');

        if ($this->total_coupon_amount > 0) {
            $total_delivery_amount = $this->total_delivery_amount - $this->total_coupon_amount;
        } else {
            $total_delivery_amount = $this->discount_type == "extra" ?
                $this->total_delivery_amount + $this->total_discount_amount : $this->total_delivery_amount - $this->total_discount_amount;
        }
        // Optimizing for pickup deatils
        if (!str_contains($request->route()->uri(), 'pickups')) {
            $assignSheet = $this->assignSheet();
            $all_sheets = $this->all_sheets();
            $lastTracking = $this->condition ? $this->lastTracking() : null;
            $way_bill_vouchers = $this->way_bill_vouchers ? $this->way_bill_vouchers()->latest()->first() : null;
            $attachments = AttachmentCollection::make($this->whenLoaded('attachments'));
            $insurance_fee  = getInsuranceFee();
        } else {
            $assignSheet = null;
            $all_sheets = null;
            $lastTracking = null;
            $way_bill_vouchers = null;
            $attachments = null;
            $insurance_fee  = null;
        }

        //$discount_amount = ($this->total_coupon_amount > 0) ? $this->total_coupon_amount : $this->total_discount_amount;
        
        $parcels = ParcelCollection::make($this->whenLoaded('parcels'));
        $discount_details = [];
        if (!str_contains($request->route()->uri(), 'pickups')) {
            $target_date_between = getVolumnTargetDateBetween();
            if ($target_date_between && $this->pickup->sender_type == "Merchant") {
                $discount_details['target_sale_count'] = (int) getTargetSaleCount();
                $discount_details['target_coupon']   = (int) getTargetCoupon();
            }
            if ($this->pickup->sender_type == "Merchant") {
                $merchant = Merchant::withTrashed()->where('id', $this->pickup->sender_id)->first();
                $discount_details['current_sale_count'] = $merchant->current_sale_count;
                $discount_details['available_coupon']   = $merchant->available_coupon;
            }
        }
        $cbm = null;
        $weight = null;
        if ($this->parcels->count() > 0) {
            $cbm = $this->parcels[0]->global_scale->cbm;
            $weight = $this->parcels[0]->weight;
            if (!str_contains($request->route()->uri(), 'pickups')) {
                if ($this->parcels[0]['discount_type_id']) {
                    // $discount_type = DiscountType::find($this->parcels[0]['discount_type_id']);
                    $discount_type = $this->parcels[0]->discount_type;
                    $discount_details['discount_name']   = $discount_type->name;
                    $discount_details['discount_amount']   = $this->parcels[0]['discount_amount'];
                    $discount_details['parcel_count']   = $this->parcels->count();
                    $discount_details['discount_type'] = $this->discount_type;
                }
            }
        }

        // $insurance_fee  = getInsuranceFee();
        $bus_fee = 0;

        if ($this->payment_type_id == 6 || $this->payment_type_id == 8) {
            $bus_fee = $this->bus_fee;
        }
        $debit_amount = 0;
        $credit_amount = 0;
        $balance = 0;

        if (!$this->condition) {
            $account_id = Pickup::find($this->pickup_id)->sender->account->id;
            $debit_amount = $this->journals()->debitAmount($this->id, $account_id);

            $credit_amount = $this->journals()->creditAmount($this->id, $account_id);

            $balance = $credit_amount - $debit_amount;
        }
        if($this->receiver_name){
            $customer = VoucherCustomerResource::make($this);
        }else{
            $customer = CustomerResource::make($this->whenLoaded('customer'));
        }
        
        return [
            'id' => $this->id,
            'receiver' => $customer,
            'receiver_name' => $this->receiver_name,
            'receiver_phone' => $this->receiver_phone,
            'receiver_other_phone' => $this->receiver_other_phone,
            'receiver_address' => $this->receiver_address,
            "pickup" => PickupResource::make($this->whenLoaded('pickup')),
            'discount_details' => $discount_details,
            'pickup_id' => $this->pickup_id,
            'voucher_no' => $this->voucher_invoice,
            // 'total_item_price' => number_format($this->total_item_price),
            // 'total_delivery_amount' => number_format($this->total_delivery_amount),
            // 'total_amount_to_collect' => number_format($this->total_amount_to_collect),
            // 'total_discount_amount' => number_format($this->total_discount_amount),
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
            'from_agent' => AgentResource::make($this->whenLoaded('from_agent')),
            'to_agent' => AgentResource::make($this->whenLoaded('to_agent')),
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
            'postpone_actor_type' => $this->postpone_actor_type,
            'parcels' => $parcels,
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
            // 'debit_amount' => number_format($debit_amount),
            // 'credit_amount' => number_format($credit_amount),
            // 'balance' => number_format($balance),
            'debit_amount' => $debit_amount,
            'credit_amount' => $credit_amount,
            'balance' => $balance,
            'lwh' => $cbm,
            'weight' => $weight,
            'attachments' => $attachments,
            'assign_sheet' => $assignSheet,
            'all_sheets' => $all_sheets,
            // 'latest_tracking' => $this->latest_tracking(),
            // 'tracking_vouchers' => TrackingVoucherCollection::make($this->whenLoaded('tracking_vouchers')),
            'latest_tracking' => $lastTracking,
            'thirdparty_invoice' => $this->thirdparty_invoice,
            'waybill_voucher' => $way_bill_vouchers,
            'outgoing_status' => $this->outgoing_status,
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d') : null,
            'platform'  => $this->platform,
            'created_by_type'  => $this->created_by_type,
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

    public function lastTracking()
    {
        $this->tracking_vouchers = TrackingVoucher::where('voucher_id', $this->id)->with(['tracking_status', 'city'])->latest()->first();
        return new TrackingVoucherResource($this->tracking_vouchers);
    }
}
