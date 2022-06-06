<?php

namespace App\Exports;

use App\Models\ReturnSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class MerchantReturnSheetData implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return ReturnSheet::filter(request()->all())
                    ->with(['merchant', 'merchant.merchant_associates' => function ($query) {
                        $query->withTrashed();
                    }, 'vouchers', 'vouchers.pickup', 'vouchers.pickup.sender', 'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone', 'vouchers.call_status',
                    'vouchers.delivery_status', 'vouchers.store_status', 'vouchers.receiver_bus_station', 'vouchers.receiver_gate', 'vouchers.sender_city', 'vouchers.sender_zone',
                    'vouchers.sender_bus_station', 'vouchers.sender_gate', 'vouchers.payment_type', 'vouchers.parcels'])
                    ->where('merchant_id', auth()->user()->id)
                    ->orderBy('id', 'desc')
                    ->get();
    }


    /**
     * @return string
     */
    public function title(): string
    {
        return 'Return Sheets';
    }

    public function map($row): array
    {
        return [
            $row->return_sheet_invoice,
            $row->qty,
            optional($row->merchant)->name,
            $row->is_returned ? 'Receive' : 'Pending',
            optional($row->created_at)->format('Y-m-d')
        ];
    }
   
    public function headings(): array
    {
        return [
            'Return Sheet',
            'Vouchers',
            'Branch',
            'Status',
            'Return Date'
        ];
    }
}
