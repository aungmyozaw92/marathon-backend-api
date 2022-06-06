<?php

namespace App\Http\Resources\SuperMerchant\MerchantAssociate;

use App\Models\ContactAssociate;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SuperMerchant\City\CityResource;
use App\Http\Resources\SuperMerchant\Zone\ZoneResource;
use App\Http\Resources\AccountInformation\AccountInformationCollection;
use App\Http\Resources\Mobile\ContactAssociate\ContactAssociateCollection;

class MerchantAssociateResource extends JsonResource
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
            'emails'       => $this->email,
            'phones'       => $this->phone,
            'address'      => $this->address,
            'zone'         => ZoneResource::make($this->zone),
            'city'         => CityResource::make($this->city),
            // 'phonesId'       => ContactAssociateCollection::make($this->whenLoaded('phones')),
            // 'emailsId'       => ContactAssociateCollection::make($this->whenLoaded('emails')),
            // 'zone'     => ZoneResource::make($this->whenLoaded('zone')),
            // 'city'         => CityResource::make($this->whenLoaded('city')),
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
