<?php

namespace App\Http\Resources\DeliSheetHistory;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\DeliSheetHistory\DeliSheetHistoryResource;
class DeliSheetHistoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $collects = DelisheetHistoryResource::class;
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
