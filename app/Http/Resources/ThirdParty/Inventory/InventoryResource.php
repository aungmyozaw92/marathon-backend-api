<?php

namespace App\Http\Resources\ThirdParty\Inventory;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ThirdParty\Product\ProductResource;
use App\Http\Resources\ThirdParty\InventoryLog\InventoryLogResource;
use App\Http\Resources\ThirdParty\InventoryLog\InventoryLogCollection;

class InventoryResource extends JsonResource
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
            
            'minimum_stock' => $this->minimum_stock,
            'qty' => $this->qty,
            'purchase_price' => $this->purchase_price,
            'sale_price' => $this->sale_price,
            'is_refundable' => $this->is_refundable,
            'is_taxable' => $this->is_taxable,
            'is_fulfilled_by' => $this->is_fulfilled_by,
            'vendor_name' => $this->vendor_name,
            'product_id' => $this->product_id,
            'product'  => ProductResource::make($this->whenLoaded('product')),
            'inventory_logs'  => InventoryLogCollection::make($this->whenLoaded('inventory_logs')),
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
