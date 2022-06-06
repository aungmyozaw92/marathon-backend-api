<?php

namespace App\Exports;

use App\Models\Journal;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class MerchantTransactionHistorySheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    protected $id;

    function __construct($id) {
        $this->id = $id;
    }

    public function collection()
    {
        return Journal::with(['resourceable',
                            'resourceable.payment_type',
                            'resourceable.delivery_status',
                            'resourceable.receiver',
                            'resourceable.receiver_city',
                            'resourceable.sender_city',
                            'resourceable.pickup.sender',
                            'credit_account','debit_account'
                        ])
                ->getTransactionJournal($this->id,request()->only([
                    'start_date', 'end_date', 
                ]))->get();
    }
    /**
     * @return string
     */
    public function title(): string
    {
        return 'MerchantTransactionHistory';
    }

    public function map($row): array
    {  
        $amount = 0;
        $resource_type = $row->resourceable_type;
        $transaction_date = $row->updated_at->format('Y-m-d');
        if ($resource_type == 'Transaction') {
            $voucher_no = $row->resourceable->transaction_no;
            $transaction_type = $row->resourceable->type;
           
            // $note = $row->resourceable->note;
            $voucher_payment_type = $row->type;
            $voucher_delivery_status = ($row->resourceable->status)? 'Comfirmed' : 'Pending';
            $collected_amount = '-';
            $delivery_fee = '-';
            $delivery_date = '-';
            $pickup_date = '-';
            $finance_date = $row->resourceable->created_at->format('Y-m-d');
            $confirm_date = $row->resourceable->updated_at->format('Y-m-d');
            $sender_name = '-';
            $receiver_name = '-';
            $receiver_city = '-';
            $receiver_zone = '-';
            $receiver_phone = '-';
            $thirdparty_invoice = '-';
             $transaction_fee = '-';
        } else {
            $voucher = $row->resourceable;
            $voucher_no = $voucher->voucher_invoice;
            $transaction_type = null;
            // $note = null;
            $voucher_payment_type = $voucher->payment_type->name;
            $voucher_delivery_status = $voucher->delivery_status->status;
            $collected_amount = $voucher->total_amount_to_collect;
            // $delivery_fee = $voucher->total_delivery_amount;
            $delivery_fee = ($voucher->discount_type == 'extra') ? 
                             $voucher->total_delivery_amount + $voucher->total_discount_amount : $voucher->total_delivery_amount - $voucher->total_discount_amount;
            
            $delivery_date = ($voucher->delivered_date)? $voucher->delivered_date->format('Y-m-d'):'-';
            $pickup_date = ($voucher->pickup->pickup_date)?  $voucher->pickup->pickup_date->format('Y-m-d'):'-';
            $finance_date = '-';
            $confirm_date = '-';
            $receiver_city = $voucher->receiver_city->name;
            $receiver_zone = $voucher->receiver_zone->name;
            $sender_name = $voucher->pickup->sender->name;
            $receiver_name = $voucher->receiver->name;
            $receiver_phone = $voucher->receiver->phone;
            $thirdparty_invoice = $voucher->thirdparty_invoice;
            $transaction_fee = $voucher->transaction_fee;
        }
        
        if ($row->debit_account->accountable_type == 'HQ') {
             if ($transaction_type == 'Withdraw') {
                $amount = $row->amount;
            }else{
                $amount = -$row->amount;
            }
        } else {
            if ($transaction_type == 'Topup') {
                $amount = -$row->amount;
            }else{
                $amount = $row->amount;
            }
        }
        return [
            $voucher_no,
            //$resource_type,
            $pickup_date,
            $delivery_date,
            $transaction_date,
            $finance_date,
            $confirm_date,
            $voucher_delivery_status,
            $sender_name,
            $receiver_name,
            $receiver_city,
            $receiver_zone,
            $receiver_phone,
            // $transaction_type,
            // $amount,
            // $row->debit_account->accountable_type,
            // $row->credit_account->accountable_type,
            $voucher_payment_type,
            $collected_amount,
            $delivery_fee,
            $amount,
            $transaction_fee,
            $thirdparty_invoice
            // $note,
            // $row->created_at->format('Y-m-d'),
            // $row->updated_at->format('Y-m-d'),
        ];
    }
   
    public function headings(): array
    {
        return [

            'Voucher/Transaction No',
            'Pickup Date',
            'Delivered Date',
            'Transaction Date',
            'Finance Date',
            'Finance Confirm Date',
            'Status',
            'Sender',	
            'Receiver',
            'Receiver City',	
            'Receiver Zone',
            'Receiver Phone',	
            'Payment Type',
            'Collect Amount',
            'Service Fee (Delivery)',
            'Amount to send',
            'Transaction Fee',
            'Thirdparty Invoice No'
        ];
    }
}

