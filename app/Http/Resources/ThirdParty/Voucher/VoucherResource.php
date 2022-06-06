<?php

namespace App\Http\Resources\ThirdParty\Voucher;

use App\Models\City;
use App\Models\Zone;
use App\Models\Pickup;
use App\Models\Merchant;
use App\Models\DiscountType;
use App\Models\TrackingVoucher;
use App\Http\Resources\Gate\GateResource;
use App\Http\Resources\Parcel\ParcelResource;
use App\Http\Resources\Pickup\PickupResource;
use App\Http\Resources\Parcel\ParcelCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\ThirdParty\City\CityResource;
use App\Http\Resources\ThirdParty\Zone\ZoneResource;
use App\Http\Resources\BusStation\BusStationResource;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Http\Resources\Merchant\MerchantCustomResource;
use App\Http\Resources\PaymentType\PaymentTypeResource;
use App\Http\Resources\StoreStatus\StoreStatusResource;
use App\Http\Resources\Mobile\Delivery\Staff\StaffResource;
use App\Http\Resources\PaymentStatus\PaymentStatusResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;
use App\Http\Resources\TrackingVoucher\TrackingVoucherResource;
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
        $this->condition = !str_contains($request->route()->uri(), 'vouchers/');
        if ($this->total_coupon_amount > 0) {
            $total_discount_amount = 0 - $this->total_coupon_amount;
        } else {
            $total_discount_amount = $this->discount_type == "extra" ?
                $this->total_discount_amount : 0 - $this->total_discount_amount;
        }
        $discount_amount = ($this->total_coupon_amount > 0) ? $this->total_coupon_amount : $this->total_discount_amount;
        // $pickup = Pickup::withTrashed()->where('id', 1)->first();
        $parcels = ParcelCollection::make($this->whenLoaded('parcels'));

        $insurance_fee  = getInsuranceFee();
        $bus_fee = 0;

        if ($this->payment_type_id == 6 || $this->payment_type_id == 8) {
            $bus_fee = $this->bus_fee;
        }
        
        return [
            'id' => $this->id,
            'voucher_no' => $this->voucher_invoice,
            'total_item_price' => $this->total_item_price,
            'total_delivery_amount' => $this->total_delivery_amount,
            'total_amount_to_collect' => $this->total_amount_to_collect,
            'total_discount_amount' => $total_discount_amount,
            'take_insurance' => ($this->insurance_fee > 0) ? 1 : 0,
            'insurance_fee' => $this->insurance_fee,
            'insurance_percentage' => $insurance_fee . '%',
            'remark' => $this->remark,

            'sender_city' => CityResource::make($this->whenLoaded('sender_city')),
            'sender_zone' => ZoneResource::make($this->whenLoaded('sender_zone')),
            'receiver_name' => $this->receiver_name,
            'receiver_phone' => $this->receiver_phone,
            'receiver_other_phone' => $this->receiver_other_phone,
            'receiver_address' => $this->receiver_address,
            'receiver' => CustomerResource::make($this->whenLoaded('customer')),
            'receiver_city' => CityResource::make($this->whenLoaded('receiver_city')),
            'receiver_zone' => ZoneResource::make($this->whenLoaded('receiver_zone')),
            'payment_type' => PaymentTypeResource::make($this->whenLoaded('payment_type')),
            
            'parcels' => $parcels,
            
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A'),
            'thirdparty_invoice' => $this->thirdparty_invoice,
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

    protected function ReturningByResource()
    {
        if ($this->pending_returning_actor_type == "Merchant") {
            return MerchantCustomResource::make($this->whenLoaded('pending_returning_actor'));
        } else {
            return StaffResource::make($this->whenLoaded('pending_returning_actor'));
        } 
    }

    public function tracking_vouchers()
    {
        $this->tracking_vouchers = TrackingVoucher::where('voucher_id', $this->id)->with(['tracking_status', 'city'])->get();
        return new TrackingVoucherCollection($this->tracking_vouchers);
    }
    public function lastTracking()
    {
        $this->tracking_vouchers = TrackingVoucher::where('voucher_id', $this->id)->with(['tracking_status', 'city'])->latest()->first();
        return new TrackingVoucherResource($this->tracking_vouchers);
    }
}
