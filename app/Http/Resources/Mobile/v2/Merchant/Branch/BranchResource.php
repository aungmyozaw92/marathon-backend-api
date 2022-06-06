<?php

namespace App\Http\Resources\Mobile\v2\Merchant\Branch;

use App\Http\Resources\Bank\BankResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\ContactAssociate\ContactAssociateCollection;

class BranchResource extends JsonResource
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
            'name'         => $this->label,
            'address'      => $this->address,
            'is_default'   => $this->is_default,
            'zone'         => $this->zone->name,
            'zone_name_mm' => $this->zone->name_mm,
            'phones'       => $this->phone,
            'emails'       => $this->email,
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
