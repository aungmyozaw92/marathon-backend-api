<?php

namespace App\Http\Resources\ThirdParty\ProductDiscount;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ThirdParty\Tag\TagResource;
use App\Http\Resources\ThirdParty\Product\ProductResource;
use App\Http\Resources\SuperMerchant\Customer\CustomerResource;
use App\Http\Resources\ThirdParty\Attachment\AttachmentCollection;

class ProductDiscountResource extends JsonResource
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
            'parcel_id' => $this->parcel_id,
            // 'merchant_id' => $this->merchant_id,
            'discount_type' => $this->discount_type,
            'amount' => $this->amount,
            'min_qty' => $this->min_qty,
            'is_inclusive' => $this->is_inclusive,
            'is_exclusive' => $this->is_exclusive,
            'is_foc' => $this->is_foc,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'created_by_type' => $this->created_by_type,   
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
