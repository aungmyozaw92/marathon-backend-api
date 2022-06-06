<?php

namespace App\Http\Resources\Mobile\Delivery\Merchant;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\Delivery\City\CityResource;
use App\Http\Resources\Mobile\Delivery\MerchantAssociate\MerchantAssociateCollection;

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
        return [
            'id'                         => $this->id,
            'name'                       => $this->name,
            'username'                   => $this->username,
            'current_sale_count'         => $this->current_sale_count,
            'available_coupon'           => $this->available_coupon,
            'is_discount'                => $this->is_discount,
            'is_allow_multiple_pickups'  => $this->is_allow_multiple_pickups,
            'rewards'                    => $this->rewards,
            'is_root_merchant'           => $this->is_root_merchant,
            'city'                       => CityResource::make($this->whenLoaded('city')),
            'branches'                   => $this->when($this->condition, MerchantAssociateCollection::make(
                $this->whenLoaded('merchant_associates')
            )),
           
            'static_price_same_city'     => $this->static_price_same_city,
            'static_price_diff_city'     => $this->static_price_diff_city,
            'static_price_branch'        => $this->static_price_branch,
            'is_corporate_merchant'      => $this->is_corporate_merchant,
            'facebook'                   => $this->facebook,
            'facebook_url'               => $this->facebook_url
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
