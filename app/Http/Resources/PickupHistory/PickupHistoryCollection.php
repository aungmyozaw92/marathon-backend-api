<?php

namespace App\Http\Resources\PickupHistory;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\PickupHistory\PickupHistoryResource;
class PickupHistoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $collects = PickupHistoryResource::class;
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
