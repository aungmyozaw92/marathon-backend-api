<?php

namespace App\Http\Resources\InvoiceJournal;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceJournalResource extends JsonResource
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
            'invoice_id' => $this->invoice_id,
            'temp_journal_id' => $this->temp_journal_id,
            'invoice_no' => $this->invoice_no,
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
            'weight' => $this->weight,
            'balance_status' => $this->balance_status,
            'adjustment_by' => $this->adjustment_by,
            'adjustment_by_name' => $this->adjustment_by_name,
            'adjustment_date' => $this->adjustment_date,
            'adjustment_note' => $this->adjustment_note,
            'adjustment_amount' => $this->adjustment_amount,
            'diff_adjustment_amount' => $this->diff_adjustment_amount,
            'delivery_status_id' => $this->delivery_status_id,
            'delivery_status' => $this->delivery_status,
            'is_dirty' => $this->is_dirty,
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
