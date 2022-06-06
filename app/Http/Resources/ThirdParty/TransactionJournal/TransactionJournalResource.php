<?php

namespace App\Http\Resources\ThirdParty\TransactionJournal;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionJournalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $amount = 0;
        $resource_type = $this->resourceable_type;
        if ($resource_type == 'Transaction') {
            $voucher_no = $this->resourceable->transaction_no;
            $transaction_type = $this->resourceable->type;
            $note = $this->resourceable->note;
            $voucher_payment_type = null;
            $voucher_delivery_status = null;
            $collected_amount = null;
            $delivery_fee = null;
            $transaction_date =  null;

        } else {
            $voucher_no = $this->resourceable->voucher_invoice;
            $transaction_type = null;
            $note = null;
            $voucher_payment_type = $this->resourceable->payment_type->name;
            $voucher_delivery_status = $this->resourceable->delivery_status->status;
            $collected_amount = $this->resourceable->total_amount_to_collect;
            $delivery_fee = $this->resourceable->total_delivery_amount;
            $transaction_date =  $this->resourceable->transaction_date ?  $this->resourceable->transaction_date->format('Y-m-d') : null;

        }
        
        if ($this->debit_account->accountable_type == 'Merchant') {
            $amount = -$this->amount;
        } else {
            $amount = $this->amount;
        }
        
        return [
            'id' => $this->id,
            'transaction_no' => $voucher_no,
            'type' => $resource_type,
            'transaction_type' => $transaction_type,
            'transaction_date' => $transaction_date,
            'amount' => $amount,
            'from_account' => $this->debit_account->accountable_type,
            'to_account' => $this->credit_account->accountable_type,
            'voucher_payment_type' => $voucher_payment_type,
            'voucher_delivery_status' => $voucher_delivery_status,
            'collected_amount' => $collected_amount,
            'delivery_fee' => $delivery_fee,
            'note' => $note,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
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
