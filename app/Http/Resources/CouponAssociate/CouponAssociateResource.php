<?php

namespace App\Http\Resources\CouponAssociate;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DiscountType\DiscountTypeCollection;

class CouponAssociateResource extends JsonResource
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
            'coupon_id' => $this->coupon_id,
            'code' => $this->code,
            'valid' => $this->valid
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
