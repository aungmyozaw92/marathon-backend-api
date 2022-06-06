<?php

namespace App\Http\Resources\Mobile\MerchantAssociate;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Mobile\MerchantAssociate\MerchantAssociateResource;

class MerchantAssociateCollection extends ResourceCollection
{
    public $collects = MerchantAssociateResource::class;

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
