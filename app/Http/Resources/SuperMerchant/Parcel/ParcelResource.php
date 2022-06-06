<?php

namespace App\Http\Resources\SuperMerchant\Parcel;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\GlobalScale\GlobalScaleResource;
use App\Http\Resources\ParcelItem\ParcelItemCollection;
use App\Http\Resources\DiscountType\DiscountTypeResource;
use App\Http\Resources\CouponAssociate\CouponAssociateResource;

class ParcelResource extends JsonResource
{
    private $condition = true;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->condition = !str_contains($request->route()->uri(), 'vouchers');
        return [
            'id'                   => $this->id,
            'weight'               => $this->weight,
            'global_scale'         => GlobalScaleResource::make($this->global_scale),
            'parcel_items'         => ParcelItemCollection::make($this->parcel_items),
        ];
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
