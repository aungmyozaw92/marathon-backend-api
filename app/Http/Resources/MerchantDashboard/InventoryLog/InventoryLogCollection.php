<?php

namespace App\Http\Resources\MerchantDashboard\InventoryLog;

use App\Http\Resources\MerchantDashboard\InventoryLog\InventoryLogResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InventoryLogCollection extends ResourceCollection
{

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = InventoryLogResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
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
