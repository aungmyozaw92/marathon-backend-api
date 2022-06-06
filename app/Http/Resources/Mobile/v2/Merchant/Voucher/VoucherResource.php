<?php

namespace App\Http\Resources\Mobile\v2\Merchant\Voucher;

use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;
use App\Http\Resources\Mobile\PaymentType\PaymentTypeResource;
use App\Http\Resources\Mobile\City\CityResource;
use App\Http\Resources\Mobile\Zone\ZoneResource;
use App\Http\Resources\Mobile\v2\Merchant\Customer\CustomerResource;
use App\Http\Resources\Mobile\v2\Merchant\Parcel\ParcelCollection;
use App\Http\Resources\Mobile\v2\Merchant\TrackingVoucher\TrackingVoucherCollection;
use App\Models\TrackingVoucher;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
	private $trackings;
    public function toArray($request)
    {
		$this->condition = !str_contains($request->route()->uri(), 'vouchers/');
		$deep_link = \Config::get('services.shareable_link.deep_link');
    	$shareable_url = $deep_link . $this->uuid;
		if ($this->total_coupon_amount > 0) {
			$receivable_delivery_amount = $this->total_delivery_amount - $this->total_coupon_amount;
		} else {
			$receivable_delivery_amount = $this->discount_type == "extra" ?
				$this->total_delivery_amount + $this->total_discount_amount : $this->total_delivery_amount - $this->total_discount_amount;
		}
		$other_fees = ($this->insurance_fee + $this->transaction_fee)-$this->total_discount_amount;
		$total_service_fees = ($receivable_delivery_amount+ $other_fees)-$this->total_discount_amount;
		if($this->payment_type_id == 1 || $this->payment_id ==10) {
			$transferable_amount = $this->total_item_price - $other_fees;
		}else if($this->payment_type_id ==2) {
			$transferable_amount = $this->total_item_price - $total_service_fees;
		}else{
			$transferable_amount = 0;
		}
		$list_info = [
			'id' => $this->id,
			'voucher_no' => $this->voucher_invoice,
			'parcels' => ParcelCollection::make($this->whenLoaded('parcels')),
			'receiver' => CustomerResource::make($this->whenLoaded('customer')),
			'receiver_city' => $this->when($this->relationLoaded('receiver_city'), function() {
				$receiver_city = CityResource::make($this->receiver_city);
				return $receiver_city->resource != null? $receiver_city->only('name','name_mm') : null;
			}),
			'receiver_zone' => $this->when($this->relationLoaded('receiver_zone'), function(){
				$receiver_zone = ZoneResource::make($this->receiver_zone);
				return $receiver_zone->resource != null ? $receiver_zone->only('name', 'name_mm') : null;
			}),

			// 'receiver_city' => $this->when($this->relationLoaded('receiver_city'), function () {
			// 	return CityResource::make($this->receiver_city)->only('id', 'name', 'name_mm');
			// // }),
			// 'receiver_zone' => $this->whenLoaded('receiver_zone', ZoneResource::make($this->receiver_zone)
			// 	->only('id', 'name', 'name_mm')),
		];
		$detail_info = [
			'thirdparty_invoice' => $this->thirdparty_invoice,
			'pickup_no' => $this->pickup ? $this->pickup_invoice : null,
			'delivery_status' => $this->when($this->relationLoaded('delivery_status'), function() {
				$delivery_status = DeliveryStatusResource::make($this->delivery_status);
				return $delivery_status->resource != null ? $delivery_status->only('status', 'status_mm') : null;
			}),
			'payment_type' => $this->when($this->relationLoaded('payment_type'),function(){
				$payment_type = PaymentTypeResource::make($this->payment_type);
				return $payment_type->resource != null ? $payment_type->only('name', 'name_mm') : null;
			}),
			'is_closed' => $this->is_closed,
			'customer_service' => auth()->user()->staff->phone,
			'total_parcel_price' => $this->total_item_price,
			'total_delivery_amount' => $this->total_delivery_amount,
			'insurance_fee' => $this->insurance_fee,
			'transaction_fee' => $this->transaction_fee,
			'total_discount_amount' => $this->total_discount_amount,
			'total_service_fee' => $total_service_fees,
			'receiver_amount_to_collect' => $this->receiver_amount_to_collect,
			'sender_amount_to_collect' => $this->sender_amount_to_collect,
			'discount_to_customer' => $this->seller_discount,
			'transferable_amount' => $transferable_amount,
			'shareable_url' => $shareable_url,
			'trackings' => !$this->condition ? $this->trackings() : null,
		];
		if($this->condition) {
			return $list_info;
		}else{
			return array_merge($list_info, $detail_info);
		}
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
	public function trackings()
	{
		$this->trackings = TrackingVoucher::where('voucher_id', $this->id)->with(['tracking_status:id,status,status_mm', 'city:id,name,name_mm'])->get();
		return new TrackingVoucherCollection($this->trackings);
	}
}
