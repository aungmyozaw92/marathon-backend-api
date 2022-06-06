<?php

namespace App\Http\Resources\ThirdParty\ProductVariation;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ThirdParty\Product\ProductResource;
use App\Http\Resources\ThirdParty\VariationMeta\VariationMetaResource;

class ProductVariationResource extends JsonResource
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
            'product'          => ProductResource::make($this->whenLoaded('product')),
            'variation_meta'          => VariationMetaResource::make($this->whenLoaded('variation_meta')),
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
