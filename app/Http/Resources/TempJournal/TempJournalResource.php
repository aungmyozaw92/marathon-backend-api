<?php

namespace App\Http\Resources\TempJournal;

use Illuminate\Http\Resources\Json\JsonResource;

class TempJournalResource extends JsonResource
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
            'journal_no' => $this->journal_no,
            'merchant_id' => $this->merchant_id,
            'debit_account_id' => $this->debit_account_id,
            'credit_account_id' => $this->credit_account_id,
            'amount' => $this->amount,
            'resourceable_id' => $this->resourceable_id,
            'resourceable_type' => $this->resourceable_type,
            'status' => $this->status,
            'thirdparty_invoice' => $this->thirdparty_invoice,
            'voucher_no' => $this->voucher_no,
            'pickup_date' => $this->pickup_date,
            'delivered_date' => $this->delivered_date,
            'receiver_name' => $this->receiver_name,
            'receiver_address' => $this->receiver_address,
            'receiver_phone' => $this->receiver_phone,
            'receiver_city' => $this->receiver_city,
            'receiver_zone' => $this->receiver_zone,
            'total_amount_to_collect' => $this->total_amount_to_collect,
            'voucher_remark' => $this->voucher_remark,
            'delivery_status_id' => ($this->delivery_status_id == 8)? "Delivered" : "Return",
            'weight' => $this->weight,
            'balance_status' => $this->balance_status,
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
