<?php

namespace App\Http\Resources\Mobile\ContactAssociate;

use App\Http\Resources\Mobile\City\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\Zone\ZoneResource;

class ContactAssociateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->type == 'phone') {
            $value = 'phone';
        }
        else if ($this->type == 'email') {
            $value = 'email';
        }

        return [
            'id'   => $this->id,
            $value => $this->value,
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
