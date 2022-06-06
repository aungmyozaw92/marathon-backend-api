<?php

namespace App\Http\Resources\ThirdParty\Merchant;

use Illuminate\Http\Resources\Json\JsonResource;

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
        $this->condition = !str_contains($request->route()->uri(), 'pickups');

        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'username'             => $this->username,
            //'current_sale_count'   => $this->current_sale_count,
            //'available_coupon'     => $this->available_coupon,
            //'balance'              => $this->account? $this->account->balance : 0,
            //'city'                 => CityResource::make($this->whenLoaded('city')),
            //'staff'                => StaffResource::make($this->whenLoaded('staff')),            
            // 'branches'             => $this->when($this->condition, MerchantAssociateCollection::make(
            //     $this->whenLoaded('merchant_associates')
            // )),
            //'discounts'          => MerchantDiscountCollection::make($this->whenLoaded('merchant_discounts')),
            //'staff'              => StaffResource::make($this->whenLoaded('staff')),
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
