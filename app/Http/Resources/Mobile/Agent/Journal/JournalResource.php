<?php

namespace App\Http\Resources\Mobile\Agent\Journal;

use Illuminate\Http\Resources\Json\JsonResource;

class JournalResource extends JsonResource
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
            $transaction_date = null;
            $receiver_name = null;
        } else {
            $voucher_no = $this->resourceable->voucher_invoice;
            $receiver_name = $this->resourceable->receiver->name;
            $transaction_date =  $this->resourceable->transaction_date ?  $this->resourceable->transaction_date->format('Y-m-d') : null;
            $transaction_type = null;
        }
        
        if ($resource_type == 'Transaction') {
            if ($this->resourceable->type == 'Withdraw') {
                $amount = -$this->amount;
            }else{
                $amount = $this->amount;
            }
        }else{
            if ($this->debit_account->accountable_type == 'HQ') {
                $amount = $this->amount;
            } else { 
                $amount = -$this->amount; 
            }
        }
        
        return [
            'id' => $this->id,
            'type' => $resource_type,
            'transaction_type' => $transaction_type,
            'transaction_date' => $transaction_date,
            'voucher_no' => $voucher_no,
            'receiver_name' => $receiver_name,
            // 'amount' => number_format($amount),
            'amount' => $amount,
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
