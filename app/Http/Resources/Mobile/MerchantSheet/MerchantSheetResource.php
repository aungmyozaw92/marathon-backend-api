<?php

namespace App\Http\Resources\Mobile\MerchantSheet;

use App\Models\MerchantSheetVoucher;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\Merchant\MerchantResource;
use App\Http\Resources\MerchantAssociate\MerchantAssociateResource;
use App\Http\Resources\Mobile\MerchantSheetVoucher\MerchantSheetVoucherCollection;

class MerchantSheetResource extends JsonResource
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
            'id' => $this->id,
            'merchantsheet_invoice' => $this->merchantsheet_invoice,
            'qty' => $this->qty,
            'credit' => $this->credit,
            'debit' => $this->debit,
            'balance' => $this->balance,
            'merchant' => MerchantResource::make($this->whenLoaded('merchant')),
            'merchant_associate' => $this->when($this->merchant_associate_id, MerchantAssociateResource::make($this->merchant_associate)),
            'is_paid' => $this->is_paid,
            'created_at' => $this->created_at->format('Y-m-d'),
            'vouchers' => MerchantSheetVoucherCollection::make($this->whenLoaded('vouchers')),
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
