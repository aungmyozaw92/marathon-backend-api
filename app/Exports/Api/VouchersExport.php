<?php

namespace App\Exports\Api;

use App\Models\Voucher;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class VouchersExport implements FromCollection,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Voucher::where('created_at', '>=', '2019-09-20')
                     ->where('created_at', '<=', '2019-09-28')->get();
    }

    public function map($row): array
    {

        return [
            $row->id,
            $row->voucher_invoice,
            $row->total_item_price,
            $row->total_delivery_amount,
            $row->total_amount_to_collect,
            $row->total_discount_amount,
            $row->total_coupon_amount,
            $row->total_bus_fee,
            $row->transaction_fee,
            $row->insurance_fee,
            $row->warehousing_fee,
            $row->total_agent_fee,
            $row->return_fee,
            $row->return_type,
            $row->sender_amount_to_collect,
            $row->receiver_amount_to_collect,
            $row->remark,
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'voucher_invoice',
            'total_item_price',
            'total_delivery_amount',
            'total_amount_to_collect',
            'total_discount_amount',
            'total_coupon_amount',
            'total_bus_fee',
            'transaction_fee',
            'insurance_fee',
            'warehousing_fee',
            'total_agent_fee',
            'return_fee',
            'return_type',
            'sender_amount_to_collect',
            'receiver_amount_to_collect',
            'remark'
        ];
    }
}
