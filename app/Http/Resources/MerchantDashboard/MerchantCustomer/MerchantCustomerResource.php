<?php

namespace App\Http\Resources\MerchantDashboard\MerchantCustomer;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\v2\Merchant\Customer\CustomerResource;

class MerchantCustomerResource extends JsonResource
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
            'id'                => $this->id,
            'customer_id'       => $this->customer_id,
            'customer'      => CustomerResource::make($this->whenLoaded('customer'))
            
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
