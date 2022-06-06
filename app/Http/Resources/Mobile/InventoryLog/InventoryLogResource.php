<?php

namespace App\Http\Resources\Mobile\InventoryLog;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MerchantDashboard\Product\ProductResource;
use App\Http\Resources\Mobile\InventoryLog\InventoryLogCollection;

class InventoryLogResource extends JsonResource
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
            'qty' => $this->qty,
            'created_by_type'  => $this->created_by_type,
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
