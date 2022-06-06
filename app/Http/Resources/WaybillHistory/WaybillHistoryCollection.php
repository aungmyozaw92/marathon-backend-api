<?php

namespace App\Http\Resources\WaybillHistory;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\WaybillHistory\WaybillHistoryResource;
class WaybillHistoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $collects = WaybillHistoryResource::class;
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
