<?php

namespace App\Exports;

use App\Models\MerchantDiscount;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class MerchantDiscountSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return MerchantDiscount::orderBy('id', 'asc')->where('merchant_id',64)->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'MerchantDiscount';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->amount,
            $row->merchant_id,
            $row->discount_type_id,
            $row->normal_or_dropoff,
            $row->extra_or_discount,
            $row->sender_city_id,
            $row->receiver_city_id,
            $row->sender_zone_id,
            $row->receiver_zone_id,
            $row->from_bus_station_id,
            $row->to_bus_station_id,
            $row->start_date,
            $row->end_date,
            $row->note,
            $row->platform,
            
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'amount',
            'merchant_id',
            'discount_type_id',
            'normal_or_dropoff',
            'extra_or_discount',
            'sender_city_id',
            'receiver_city_id',
            'sender_zone_id',
            'receiver_zone_id',
            'from_bus_station_id',
            'to_bus_station_id',
            'start_date',
            'end_date',
            'note',
            'platform',
        ];
    }

}
