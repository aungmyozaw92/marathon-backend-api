<?php

namespace App\Http\Resources\Mobile\ContactAssociate;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Mobile\ContactAssociate\ContactAssociateResource;

class ContactAssociateCollection extends ResourceCollection
{
    public $collects = ContactAssociateResource::class;

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
