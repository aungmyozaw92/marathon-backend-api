<?php

namespace App\Exports;

use App\Models\Voucher;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class VoucherSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return Voucher::with(['pickup','receiver','parcels','parcels.parcel_items','receiver_city','receiver_zone'])
                        ->whereIn('id', [
                            875,
                           
                            ])
                        ->orderBy('id', 'asc')->get();
    }
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Voucher';
    }

    public function map($row): array
    {
        
        $receiver = $row->receiver;
        $parcel = $row->parcels[0];
        $parcel_item = $parcel['parcel_items'][0];
        return [
            $receiver->name,
            $receiver->phone,
            $row->receiver_city->name,
            $row->receiver_zone->name,
            $parcel['global_scale_id'],
            $parcel['weight'],
            $receiver->address,
            $parcel_item['item_name'],
            $parcel_item['item_qty'],
            $row->payment_type->name,
            $parcel_item['item_price'],
            $row->thirdparty_invoice,
            $row->voucher_invoice . '&&' . $row->remark,
            $row->parcels->count(),
            $row->delivery_status_id,
            $row->total_delivery_amount,
            $row->total_amount_to_collect,
            $row->total_item_price,
            $row->created_by_id,
            $row->created_by_type,
            $row->pickup?$row->pickup->sender_type:null,
            $row->pickup?$row->pickup->sender->name:null
        ];
    }
   
    public function headings(): array
    {
        return [
            'Name',
            'Phone number',
            'City',
            'Zone',
            'LWH',
            'Weight',
            'Address',
            'Item Name',
            'Item Qty',
            'Payment Type',
            'Amount',
            'Thirdparty Invoice',
            'Remark',
            'Parcel Count',
            'Delivery Status',
            'total_delivery_amount',
            'total_amount_to_collect',
            'total_item_price',
            'created_by_id',
            'created_by_type',
            'sender_type',
            'sender name'
        ];
    }

}
