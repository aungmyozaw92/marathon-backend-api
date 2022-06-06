<?php

namespace App\Http\Resources\Mobile\v2\Merchant\Transaction;

use App\Http\Resources\Bank\BankResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\v2\Merchant\Attachment\AttachmentCollection;

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
            'extra_amount' => $this->extra_amount,
            'transaction_type' => $this->type,
            'status' => ($this->status) ? 'Confirmed' : 'Pending',
            'transaction_option' => $this->account_name .' - '.$this->account_no,
            'note' => $this->note,
            'requested_date' => $this->created_at->format('Y-m-d'),
            'confirmed_date' => $this->updated_at->format('Y-m-d'),
            'attachments' => AttachmentCollection::make($this->whenLoaded('attachments'))
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
