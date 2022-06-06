<?php

namespace App\Http\Resources\Mobile\MerchantJournal;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantJournalResource extends JsonResource
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
            $voucher_id = null;
            $transaction_type = $this->resourceable->type;
            $note = $this->resourceable->note;
            // //$voucher_payment_type = null;
            $voucher_delivery_status = null;
            $receiver_name = null;
            $receiver_address =  null;
			$receiver_phone =  null;
			$transaction_status = $this->resourceable->status;
			$voucher_payment_type = null;
			$transaction_document = $this->resourceable->firestore_document;
			$voucher_document = null;
        } else {
            $voucher_no = $this->resourceable->voucher_invoice;
            $voucher_id = $this->resourceable->id;
            $transaction_type = null;
            $note = null;
            // //$voucher_payment_type = $this->resourceable->payment_type->name;
            $voucher_delivery_status = $this->resourceable->delivery_status->status;
            $receiver_name = $this->resourceable->receiver->name;
            $receiver_address = $this->resourceable->receiver->address;
			$receiver_phone = $this->resourceable->receiver->phone;
			$transaction_status = null;
			$voucher_payment_type = $this->resourceable->payment_type->name_mm;
			$transaction_document = null;
			$voucher_document = $this->resourceable->firestore_document;
        }
        
        if ($this->debit_account->accountable_type == 'Merchant') {
            $payable_amount = $this->amount;
            $receiveable_amount = 0;
        } else {
            $payable_amount = 0;
            $receiveable_amount = $this->amount;
        }
        
        return [
            'id' => $this->id,
            'voucher_no' => $voucher_no,
            'voucher_id' => $voucher_id,
            'type' => $resource_type,
            'transaction_type' => $transaction_type,
            // 'payable_amount' => number_format($payable_amount),
            // 'receiveable_amount' => number_format($receiveable_amount),
            'payable_amount' => $payable_amount,
            'receiveable_amount' => $receiveable_amount,
            //'voucher_payment_type' => $voucher_payment_type,
            'voucher_delivery_status' => $voucher_delivery_status,
            'receiver_name' => $receiver_name,
            'receiver_address' => $receiver_address,
            'receiver_phone' => $receiver_phone,
            'note' => $note,
            'created_at' => $this->created_at->format('Y-m-d'),
			'updated_at' => $this->updated_at->format('Y-m-d'),
			'transaction_status' => $transaction_status,
			'voucher_payment_type' => $voucher_payment_type,
			'transaction_document' => $transaction_document,
			'voucher_document' => $voucher_document
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
