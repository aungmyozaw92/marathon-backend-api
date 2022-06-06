<?php

namespace App\Http\Resources\MerchantRateCard;

use App\Models\Transaction;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\Account\AccountResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\AgentBadge\AgentBadgeResource;
use App\Http\Resources\DiscountType\DiscountTypeResource;
use App\Http\Resources\MerchantAssociate\MerchantAssociateResource;

class MerchantRateCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                 => $this->id,
            'amount'             => $this->amount,
            'platform'           => $this->platform,
            'discount_type'      => DiscountTypeResource::make($this->whenLoaded('discount_type')),
            'sender_city'        => $this->sender_city ? CityResource::make($this->whenLoaded('sender_city'))->only(['id','name','name_mm']) : null,
            'receiver_city'      => $this->receiver_city ? CityResource::make($this->whenLoaded('receiver_city'))->only(['id','name','name_mm']) : null,
            'sender_zone'       => $this->sender_zone ? ZoneResource::make($this->whenLoaded('sender_zone'))->only(['id','name','name_mm']) : null,
            'receiver_zone'      => $this->receiver_zone ? ZoneResource::make($this->whenLoaded('receiver_zone'))->only(['id','name','name_mm']) : null,
            'merchant'           => $this->merchant ? MerchantResource::make($this->whenLoaded('merchant'))->only(['id','name']) : null,
            'merchant_associate' => $this->merchant_associate ? MerchantAssociateResource::make($this->whenLoaded('merchant_associate'))->only(['id','label']) : null,
            'note'               => $this->note,
            'incremental_weight' => $this->incremental_weight,
            'from_weight'        => $this->from_weight,
            'to_weight'          => $this->to_weight,
            
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
