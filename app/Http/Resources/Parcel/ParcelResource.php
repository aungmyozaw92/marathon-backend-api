<?php

namespace App\Http\Resources\Parcel;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\GlobalScale\GlobalScaleResource;
use App\Http\Resources\ParcelItem\ParcelItemCollection;
use App\Http\Resources\DiscountType\DiscountTypeResource;
use App\Http\Resources\CouponAssociate\CouponAssociateResource;

class ParcelResource extends JsonResource
{
    private $condition = true;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->condition = !str_contains($request->route()->uri(), 'vouchers');
        return [
            'id'                   => $this->id,
            'voucher_id'           => $this->voucher_id,
            'weight'               => $this->weight,
            'parcel_items'         => ParcelItemCollection::make($this->parcel_items),
            'global_scale'         => GlobalScaleResource::make($this->global_scale),
            'discount_type'        => DiscountTypeResource::make($this->discount_type),
            'coupon'               => CouponAssociateResource::make($this->coupon_associate),
            // 'cal_parcel_price'     => number_format($this->cal_parcel_price),
            // 'cal_delivery_price'   => number_format($this->cal_delivery_price),
            // 'cal_gate_price'       => number_format($this->cal_gate_price),
            // 'discount_price'       => number_format($this->discount_price),
            // 'coupon_price'         => number_format($this->coupon_price),
            // 'label_parcel_price'   => number_format($this->label_parcel_price),
            // 'label_delivery_price' => number_format($this->label_delivery_price),
            // 'label_gate_price'     => number_format($this->label_gate_price),
            // 'sub_total'            => number_format($this->sub_total),
            'cal_parcel_price'     => $this->cal_parcel_price,
            'cal_delivery_price'   => $this->cal_delivery_price,
            'cal_gate_price'       => $this->cal_gate_price,
            'discount_price'       => $this->discount_price,
            'coupon_price'         => $this->coupon_price,
            'label_parcel_price'   => $this->label_parcel_price,
            'label_delivery_price' => $this->label_delivery_price,
            'label_gate_price'     => $this->label_gate_price,
            'sub_total'            => $this->sub_total,
            'origin_lwh'           => $this->origin_lwh,
            'origin_weight'        => $this->origin_weight,
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => 1,
        ];
    }
}
