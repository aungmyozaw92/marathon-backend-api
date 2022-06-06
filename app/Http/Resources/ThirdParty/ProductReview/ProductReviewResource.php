<?php

namespace App\Http\Resources\ThirdParty\ProductReview;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ThirdParty\Tag\TagResource;
use App\Http\Resources\ThirdParty\Product\ProductResource;
use App\Http\Resources\SuperMerchant\Customer\CustomerResource;
use App\Http\Resources\ThirdParty\Attachment\AttachmentCollection;

class ProductReviewResource extends JsonResource
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
            'rating' => $this->rating,
            'note' => $this->note,
            'attachemnts' => AttachmentCollection::make($this->whenLoaded('attachemnts')),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'product' => ProductResource::make($this->whenLoaded('product')), 
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
