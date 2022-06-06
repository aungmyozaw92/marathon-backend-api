<?php

namespace App\Http\Resources\HqTransaction;

use Illuminate\Http\Resources\Json\JsonResource;

class HqTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->from_account->accountable_type !='HQ') {
            $from_account_name = $this->from_account->accountable->name;
            $from_account_type = $this->from_account->accountable_type;
        }else{
            $from_account_name = 'HQ';
            $from_account_type = 'HQ';
        }
        if ($this->to_account->accountable_type !='HQ') {
            $to_account_name = $this->to_account->accountable->name;
            $to_account_type = $this->to_account->accountable_type;
        }else{
            $to_account_name = 'HQ';
            $to_account_type = 'HQ';
        }
        return [
            'id' => $this->id,
            'transaction_no' => $this->transaction_no,
            'amount' => $this->amount,
            'extra_amount' => $this->extra_amount,
            'type' => $this->type,
            'status' => $this->status,
            'note' => $this->note,
            'from_account_name' => $from_account_name,
            'from_account_type' => $from_account_type,
            'to_account_name' => $to_account_name,
            'to_account_type' => $to_account_type,
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
