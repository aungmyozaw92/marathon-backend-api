<?php

namespace App\Http\Resources\Mobile\MerchantAssociate;

use App\Http\Resources\Mobile\City\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\Zone\ZoneResource;
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
            'label'        => $this->label,
            'phones'       => ContactAssociateCollection::make($this->whenLoaded('phones')),
            'emails'       => ContactAssociateCollection::make($this->whenLoaded('emails')),
            'address'      => $this->address,
            // 'address'      => getConvertedUni2Zg($this->address),
            'zone'         => ZoneResource::make($this->whenLoaded('zone')),
            'city'         => CityResource::make($this->whenLoaded('city')),
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
