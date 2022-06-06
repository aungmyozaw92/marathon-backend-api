<?php

namespace App\Http\Resources\Transaction;

use App\Http\Resources\Bank\BankResource;
use App\Http\Resources\Account\AccountResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\Merchant\MerchantCustomResource;
use App\Http\Resources\AccountInformation\AccountInformationResource;

class TransactionResource extends JsonResource
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
            'transaction_no' => $this->transaction_no,
            // 'amount' => number_format($this->amount),
            // 'extra_amount' => number_format($this->extra_amount),
            'amount' => $this->amount,
            'extra_amount' => $this->extra_amount,
            'type' => $this->type,
            'order_id' => $this->order_id,
            'order_payment_option' => $this->order_payment_option,
            'order_payment_method' => $this->order_payment_method,
            'status' => $this->status,
            'note' => $this->note,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
            'from_account' => AccountResource::make($this->whenLoaded('from_account')),
            'to_account' => AccountResource::make($this->whenLoaded('to_account')),
            'attachments' => AttachmentCollection::make($this->whenLoaded('attachments')),
            'account_information' => AccountInformationResource::make($this->whenLoaded('account_information')),
            'account_name'      => $this->account_name,
            'account_no'        => $this->account_no,
            'created_by_id'        => $this->created_by_id,
            'created_by_type'        => $this->created_by_type,
            'bank'              => BankResource::make($this->whenLoaded('bank')),
            'created_by_merchant'              => MerchantCustomResource::make($this->whenLoaded('created_by_merchant'))
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
