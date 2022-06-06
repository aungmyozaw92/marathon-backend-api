<?php

namespace App\Http\Resources\Mobile\Store;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\Merchant\MerchantResource;

class StoreResource extends JsonResource
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
            'uuid' => $this->uuid,
            'item_name' => $this->item_name,
            // 'item_price' => number_format($this->item_price),
            'item_price' => $this->item_price,
            'merchant' => MerchantResource::make($this->whenLoaded('merchant')),
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
