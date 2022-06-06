<?php

namespace App\Http\Resources\Coupon;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DiscountType\DiscountTypeResource;
use App\Http\Resources\CouponAssociate\CouponAssociateCollection;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            // 'amount' => number_format($this->amount),
            'amount' => $this->amount,
            'valid_date' => $this->valid_date,
            'discount_type' => DiscountTypeResource::make($this->discount_type),
            'coupon_associates' => CouponAssociateCollection::make($this->coupon_associates),
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
