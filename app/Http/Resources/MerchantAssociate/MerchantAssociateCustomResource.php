<?php

namespace App\Http\Resources\MerchantAssociate;

use App\Models\ContactAssociate;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Zone\ZoneResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AccountInformation\AccountInformationCollection;
use App\Http\Resources\Mobile\ContactAssociate\ContactAssociateCollection;

class MerchantAssociateCustomResource extends JsonResource
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
            'id'           => $this->id,
            'merchant_id'  => $this->merchant_id,
            'label'        => $this->label,
            // 'emails'       => $this->email,
            'phones'       => $this->phones->pluck('value')->toArray(),
            'address'      => $this->address,
            'is_default'   => $this->is_default,
            // 'account_informations' => AccountInformationCollection::make($this->whenLoaded('account_informations')),
            // 'zone'         => ZoneResource::make($this->zone),
            // 'city'         => CityResource::make($this->city),
           
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
