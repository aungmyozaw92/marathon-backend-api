<?php

namespace App\Http\Resources\InvoiceHistory;

use App\Http\Resources\InvoiceHistory\InvoiceHistoryResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InvoiceHistoryCollection extends ResourceCollection
{

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = InvoiceHistoryResource::class;

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
