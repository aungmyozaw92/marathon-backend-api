<?php

namespace App\Http\Resources\SuperMerchant\Pickup;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SuperMerchant\Voucher\VoucherResource;
use App\Http\Resources\SuperMerchant\Merchant\MerchantResource;
use App\Http\Resources\SuperMerchant\Voucher\VoucherCollection;
use App\Http\Resources\SuperMerchant\MerchantAssociate\MerchantAssociateResource;

class PickupResource extends JsonResource
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
            'id' => $this->id,
            'pickup_invoice' => $this->pickup_invoice,
            'is_pickuped' => $this->is_pickuped,
            'priority' => $this->priority,
            'merchant' => MerchantResource::make($this->whenLoaded('sender')),
            'merchant_associate' => $this->when($this->sender_associate_id, MerchantAssociateResource::make($this->sender_associate)),
            'vouchers' => VoucherCollection::make($this->whenLoaded('vouchers')),
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
