<?php

namespace App\Http\Resources\Mobile\Delivery\MerchantAssociate;

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
            'phones'       => $this->phone,
            'emails'       => $this->email,
            'address'      => $this->address,
            // 'address'      => getConvertedUni2Zg($this->address),
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
