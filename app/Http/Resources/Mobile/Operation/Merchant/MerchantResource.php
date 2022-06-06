<?php

namespace App\Http\Resources\Mobile\Operation\Merchant;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\Operation\Staff\StaffResource;
use App\Http\Resources\Mobile\MerchantDiscount\MerchantDiscountCollection;
use App\Http\Resources\Mobile\MerchantAssociate\MerchantAssociateCollection;

class MerchantResource extends JsonResource
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
        $this->condition = ! str_contains($request->route()->uri(), 'pickups');
    
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'username'           => $this->username,
            'city_id'            => $this->city_id,
            'staff'              => StaffResource::make($this->whenLoaded('staff')),
            'discounts'           => MerchantDiscountCollection::make($this->whenLoaded('merchant_discounts')),
            'branches'           => $this->when(
                $this->condition,
                MerchantAssociateCollection::make(
                    $this->whenLoaded('merchant_associates')
                )
            ),
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
