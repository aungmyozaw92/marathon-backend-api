<?php

namespace App\Exports;

use App\Models\Journal;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class AgentTransactionHistorySheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    protected $id;

    function __construct($id) {
        $this->id = $id;
    }

    public function collection()
    {
        return  Journal::with(['resourceable',
                            'resourceable.payment_type',
                            'resourceable.delivery_status',
                            'resourceable.receiver',
                            'resourceable.receiver_city',
                            'resourceable.sender_city',
                            'resourceable.pickup.sender',
                            'credit_account','debit_account'
                            ])
                        ->getTransactionJournal($this->id, request()->only([
                            'start_date', 'end_date',
                        ]))->get();
    }
    /**
     * @return string
     */
    public function title(): string
    {
        return 'AgentTransactionHistory';
    }

    public function map($row): array
    {  
        $amount = 0;
        $resource_type = $row->resourceable_type;
        if ($resource_type == 'Transaction') {
            $voucher_no = $row->resourceable->transaction_no;
            $transaction_type = $row->resourceable->type;
            $note = $row->resourceable->note;
            $voucher_payment_type = null;
            $voucher_delivery_status = null;
            $collected_amount = null;
            $delivery_fee = null;
            $from_city = null;
            $to_city = null;
            $sender_name = null;
            $receiver_name = null;
            $delivered_date = null;
            $confirm_date = ($row->resourceable->status) ? $row->updated_at->format('Y-m-d H:i:s') : null;
        } else {
            $voucher_no = $row->resourceable->voucher_invoice;
            $transaction_type = null;
            $note = null;
            $voucher_payment_type = $row->resourceable->payment_type->name;
            $voucher_delivery_status = $row->resourceable->delivery_status->status;
            $from_city = $row->resourceable->sender_city->name;
            $to_city = $row->resourceable->receiver_city->name;
            $sender_name = $row->resourceable->pickup->sender->name;
            $receiver_name = $row->resourceable->receiver->name;
            $collected_amount = ($row->resourceable->total_amount_to_collect > 0) ? $row->resourceable->total_amount_to_collect : '0';
            $delivered_date = $row->resourceable->delivered_date;
            $delivery_fee = ($row->resourceable->discount_type == 'extra') ? 
                             $row->resourceable->total_delivery_amount + $row->resourceable->total_discount_amount : $row->resourceable->total_delivery_amount - $row->resourceable->total_discount_amount;
            // $delivery_fee = ($delivery_fee > 0) ? $delivery_fee : '0'
            $confirm_date = null;
        }
        
        if ($row->debit_account->accountable_type == 'HQ') {
            $amount = -$row->amount;
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
            $sender_name,
            $receiver_name,
            $from_city,
            $to_city,
            $collected_amount,
            $delivery_fee,
            $amount,
            $voucher_payment_type,
            $voucher_delivery_status,
            $row->created_at,
            $delivered_date,
            $note,
            
        ];
    }
   
    public function headings(): array
    {
        return [

            'Voucher/Transaction No',
            'Sender',
            'Receiver',
            'Sender City',
            'Receiver City',
            'Collected Amount',
            'Service Fee',	
            'Balance',
            'Payment Type', 
            'Status',
            'Date (Created At)',
            'Delivered Date',
            'Note',
        ];
    }
}

