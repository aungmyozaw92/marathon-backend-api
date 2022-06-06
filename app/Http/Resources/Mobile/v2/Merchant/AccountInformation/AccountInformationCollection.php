<?php

namespace App\Http\Resources\Mobile\v2\Merchant\AccountInformation;

use App\Http\Resources\Mobile\v2\Merchant\AccountInformation\AccountInformationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AccountInformationCollection extends ResourceCollection
{

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = AccountInformationResource::class;

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