<?php

namespace App\Http\Resources\MerchantDashboard\OrderItem;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Merchant\MerchantResource;

class OrderItemResource extends JsonResource
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
            'id'                        => $this->id,
            'product_id'            => $this->product_id,
            'item_name'                      => $this->name,
            'item_qty'                      => $this->qty,
            'item_price'                      => $this->price,
            'weight'                      => $this->weight,
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
