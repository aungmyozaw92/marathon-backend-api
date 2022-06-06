<?php

namespace App\Http\Resources\ThirdParty\Product;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ThirdParty\Inventory\InventoryResource;
use App\Http\Resources\ThirdParty\ProductTag\ProductTagCollection;
use App\Http\Resources\ThirdParty\ProductType\ProductTypeResource;
use App\Http\Resources\ThirdParty\ProductReview\ProductReviewCollection;
use App\Http\Resources\MerchantDashboard\Attachment\AttachmentCollection;
use App\Http\Resources\ThirdParty\ProductVariation\ProductVariationCollection;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        ;
        // sum(product_reviews.rating)/number_review
        $ratings = $this->product_reviews->sum('rating');
        
        $total_review = ($ratings > 0)? $ratings/$this->product_reviews->count() : 0;
        return [
            'id'                => $this->id,
            // 'uuid'              => $this->uuid,
            'sku'              => $this->sku,
            // 'merchant_id'       => $this->merchant_id,
            'item_name'         => $this->item_name,
            'item_price'        => $this->item_price,
            'is_seasonal'       => $this->is_seasonal,
            'is_feature'        => $this->is_feature,
            'lwh'               => $this->lwh,
            'weight'            => $this->weight,
            'product_type_id'   => $this->product_type_id,
            'total_review'      => $total_review,
            'product_type'      => ProductTypeResource::make($this->whenLoaded('product_type')),
            'inventory'         => InventoryResource::make($this->whenLoaded('inventory')),
            'attachments'       => AttachmentCollection::make($this->whenLoaded('attachments')),
            'product_tags'      => ProductTagCollection::make($this->whenLoaded('product_tags')),
            'product_variations' => ProductVariationCollection::make($this->whenLoaded('product_variations')),
            'product_reviews'    => ProductReviewCollection::make($this->whenLoaded('product_reviews'))
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
