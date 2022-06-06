<?php

namespace App\Http\Resources\MerchantDashboard\ProductTag;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MerchantDashboard\Tag\TagResource;
use App\Http\Resources\MerchantDashboard\Product\ProductResource;

class ProductTagResource extends JsonResource
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
            'id'   => $this->id,
            'tag'  => TagResource::make($this->whenLoaded('tag')),
            'product'  => ProductResource::make($this->whenLoaded('product')),
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
