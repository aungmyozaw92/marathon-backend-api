<?php

namespace App\Http\Resources\ReturnSheetHistory;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\ReturnSheetHistory\ReturnSheetHistoryResource;

class ReturnSheetHistoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $collects = ReturnSheetHistoryResource::class;
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
