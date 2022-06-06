<?php

namespace App\Http\Resources\AttachmentInvoice;

use App\Http\Resources\AttachmentInvoice\AttachmentInvoiceResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AttachmentInvoiceCollection extends ResourceCollection
{

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = AttachmentInvoiceResource::class;

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
