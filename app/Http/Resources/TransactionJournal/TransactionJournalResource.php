<?php

namespace App\Http\Resources\TransactionJournal;

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
            $status = $this->resourceable->status;
            $note = $this->resourceable->note;
            $voucher_payment_type = null;
            $thirdparty_invoice = null;
            $voucher_delivery_status = null;
            $collected_amount = null;
            $delivery_fee = null;
            $from_city = null;
            $to_city = null;
            $sender_name = null;
            $receiver_name = null;
            $delivered_date = null;
            $confirm_date = ($this->resourceable->status) ? $this->updated_at->format('Y-m-d H:i:s') : null;
        } else {
            $voucher_no = $this->resourceable->voucher_invoice;
            $thirdparty_invoice = $this->resourceable->thirdparty_invoice;
            $transaction_type = null;
            $note = null;
            $status = $this->status;
            $voucher_payment_type = $this->resourceable->payment_type->name;
            $voucher_delivery_status = $this->resourceable->delivery_status->status;
            $from_city = $this->resourceable->sender_city->name;
            $to_city = $this->resourceable->receiver_city->name;
            $sender_name = $this->resourceable->pickup->sender->name;
            $receiver_name = $this->resourceable->receiver->name;
            $collected_amount = $this->resourceable->total_amount_to_collect;
            $delivered_date = $this->resourceable->delivered_date;
            $delivery_fee = ($this->resourceable->discount_type == 'extra') ? 
                             $this->resourceable->total_delivery_amount + $this->resourceable->total_discount_amount : $this->resourceable->total_delivery_amount - $this->resourceable->total_discount_amount;
            $confirm_date = null;
        }
        
        if ($this->debit_account->accountable_type == 'HQ') {
            if ($transaction_type == 'Withdraw') {
                $amount = $this->amount;
            }else{
                $amount = -$this->amount;
            }
        } else {
            if ($transaction_type == 'Topup') {
                $amount = -$this->amount;
            }else{
                $amount = $this->amount;
            }
        }
        
        return [
            'id' => $this->id,
            'transaction_no' => $voucher_no,
            'transaction_id' => $this->resourceable_id,
            'thirdparty_invoice' => $thirdparty_invoice,
            'type' => $resource_type,
            'from_city' => $from_city,
            'to_city' => $to_city,
            'sender_name' => $sender_name,
            'receiver_name' => $receiver_name,
            'status' => $status,
            'transaction_type' => $transaction_type,
            'amount' => $amount,
            'from_account' => $this->debit_account->accountable_type,
            'to_account' => $this->credit_account->accountable_type,
            'voucher_payment_type' => $voucher_payment_type,
            'voucher_delivery_status' => $voucher_delivery_status,
            'collected_amount' => $collected_amount,
            'delivery_fee' => $delivery_fee,
            'delivered_date' => $delivered_date ? $delivered_date->format('Y-m-d H:i:s') : null,
            'note' => $note,
            'journal_created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'journal_updated_at' => $this->updated_at ->format('Y-m-d H:i:s'),
            'transaction_comfirm_date' => $confirm_date,
            'created_at' => $this->resourceable->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->resourceable->updated_at->format('Y-m-d H:i:s'),


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
