<?php

namespace App\Http\Resources\Mobile\v2\Merchant\ReturnSheet;

use App\Http\Resources\Mobile\v2\Merchant\ReturnSheet\ReturnSheetResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ReturnSheetCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = ReturnSheetResource::class;

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
