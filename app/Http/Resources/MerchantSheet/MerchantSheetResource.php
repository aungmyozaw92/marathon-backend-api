<?php

namespace App\Http\Resources\MerchantSheet;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\MerchantAssociate\MerchantAssociateResource;
use App\Http\Resources\MerchantSheetVoucher\MerchantSheetVoucherCollection;

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
            // 'credit' => number_format($this->credit),
            // 'debit' => number_format($this->debit),
            // 'balance' => number_format($this->balance),
            'credit' => $this->credit,
            'debit' => $this->debit,
            'balance' => $this->balance,
            'merchant' => MerchantResource::make($this->whenLoaded('merchant')),
            'merchant_associate' => $this->when($this->merchant_associate_id, MerchantAssociateResource::make($this->merchant_associate)),
            'vouchers' => MerchantSheetVoucherCollection::make($this->whenLoaded('vouchers')),
            'is_paid' => $this->is_paid,
            'note'  => $this->note,
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A')
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
