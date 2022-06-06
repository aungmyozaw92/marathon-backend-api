<?php

namespace App\Http\Resources\Invoice;

use App\Http\Resources\City\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\Merchant\MerchantCustomResource;
use App\Http\Resources\InvoiceJournal\InvoiceJournalResource;
use App\Http\Resources\InvoiceJournal\InvoiceJournalCollection;
use App\Http\Resources\AttachmentInvoice\AttachmentInvoiceCollection;

class InvoiceResource extends JsonResource
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
            'invoice_no' => $this->invoice_no,
            'merchant_id' => $this->merchant_id,
            'city_id' => $this->city_id,
            'merchant' => MerchantCustomResource::make($this->whenLoaded('merchant')),
            'city' => CityResource::make($this->whenLoaded('city')),
            'invoice_journals' => InvoiceJournalCollection::make($this->whenLoaded('invoice_journals')),
            'attachments' => AttachmentInvoiceCollection::make($this->whenLoaded('attachments')),
            'total_voucher' => $this->total_voucher,
            'total_amount' => $this->total_amount,
            'tax' => $this->tax,
            'tax_amount' => ($this->tax)? $this->total_amount * 0.05 : 0,
            'payment_status' => $this->payment_status,
            'note' => $this->note,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
           
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
