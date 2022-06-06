<?php

namespace App\Http\Resources\MerchantDashboard\Product;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ThirdParty\Inventory\InventoryResource;
use App\Http\Resources\MerchantDashboard\Attachment\AttachmentCollection;
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
            'merchant_id'       => $this->merchant_id,
            'item_name'         => $this->item_name,
            'item_price'        => $this->item_price,
            'is_seasonal'       => $this->is_seasonal,
            'is_feature'        => $this->is_feature,
            'lwh'               => $this->lwh,
            'weight'            => $this->weight,
            'product_type'      => ProductTypeResource::make($this->whenLoaded('product_type')),
            'inventory'         => InventoryResource::make($this->whenLoaded('inventory')),
            'attachments'       => AttachmentCollection::make($this->whenLoaded('attachments'))
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
