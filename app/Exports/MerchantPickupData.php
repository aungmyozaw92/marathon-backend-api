<?php

namespace App\Exports;

use App\Models\Pickup;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class MerchantPickupData implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return Pickup::with('opened_by_staff', 'sender', 'created_by')
                ->where('sender_type', 'Merchant')
                // ->where('created_by_type', 'Merchant')
                ->where('sender_id', auth()->user()->id)
                ->withCount('vouchers')
                ->filter(request()->only([
                    'year', 'month', 'day', 'sender_name', 'sender_phone', 'sender_address',
                    'opened_by', 'note', 'search', 'is_pickuped'
                ]))
                ->orderBy('id', 'desc')
                ->get();
    }


    /**
     * @return string
     */
    public function title(): string
    {
        return 'Pickups';
    }

    public function map($row): array
    {
        return [
            $row->pickup_invoice,
            $row->vouchers_count,
            $row->is_pickuped ? 'Picked' : 'Pending',
            optional($row->sender_associate)->label,
            optional($row->created_at)->format('Y-m-d'),
            optional($row->pickup_date)->format('Y-m-d'),
            $row->note,
            $row->created_by_type == "Merchant" ? 'Online' : 'Marathon'
        ];
    }
   
    public function headings(): array
    {
        return [
            'Pickup-ID',
            'Order Qty',
            'Status',
            'Branch',
            'Created at',
            'Pickup at',
            'Note',
            'Create By'
        ];
    }
}
