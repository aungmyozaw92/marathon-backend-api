<?php

namespace App\Http\Resources\ThirdParty\Transaction;

use App\Http\Resources\Account\AccountResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ThirdParty\TransactionAttachment\TransactionAttachmentCollection;

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
            'amount' => $this->amount,
            'type' => $this->type,
            'status' => $this->status,
            'note' => $this->note,
            'account_name' => $this->account_name,
            'account_no' => $this->account_no,
            // 'created_by' => $this->created_by,
            // 'updated_by' => $this->updated_by,
            'created_at' => $this->created_at->format('Y-m-d'),
            // 'updated_at' => $this->updated_at->format('Y-m-d'),
            'confirm_date' => ($this->status) ? $this->updated_at->format('Y-m-d') : null,
            // 'from_account' => AccountResource::make($this->whenLoaded('from_account')),
            // 'to_account' => AccountResource::make($this->whenLoaded('to_account')),
            'attachments' => TransactionAttachmentCollection::make($this->whenLoaded('attachments')),
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
