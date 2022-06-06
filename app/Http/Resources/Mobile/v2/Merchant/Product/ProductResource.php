<?php

namespace App\Http\Resources\Mobile\v2\Merchant\Product;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\v2\Merchant\Attachment\AttachmentResource;
use App\Http\Resources\MerchantDashboard\ProductType\ProductTypeResource;

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
        return [
            'id'                => $this->id,
            'uuid'              => $this->uuid,
            'sku'              => $this->sku,
            // 'merchant_id'       => $this->merchant_id,
            'item_name'         => $this->item_name,
            'item_price'        => $this->item_price,
            'is_seasonal'       => $this->is_seasonal,
            'is_feature'        => $this->is_feature,
            'lwh'               => $this->lwh,
            'weight'            => $this->weight,
            'product_type'      => ProductTypeResource::make($this->whenLoaded('product_type')),
            'attachment'       => AttachmentResource::make($this->whenLoaded('attachment'))
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
