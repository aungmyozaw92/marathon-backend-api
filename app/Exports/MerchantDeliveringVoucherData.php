<?php

namespace App\Exports;

use App\Models\Voucher;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class MerchantDeliveringVoucherData implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return Voucher::with([
                    'pickup', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                    'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
                    'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration', 'delegate_person', 'parcels' => function ($query) {
                        $query->withTrashed();
                    }
                ])
            // ->where('created_by_id', auth()->user()->id)
            // ->where('created_by_type', 'Merchant')
            ->whereNotIn('delivery_status_id', [8, 9])
            ->whereHas('pickup', function ($qr) {
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->filter(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'pickup_date'
            ]))
            ->order(request()->only([
                'sortBy', 'orderBy'
            ]))
            ->get();
    }


    /**
     * @return string
     */
    public function title(): string
    {
        return 'Delivering Vouchers';
    }

    public function map($row): array
    {
        if ($row->total_coupon_amount > 0) {
            $total_delivery_amount = $row->total_delivery_amount - $row->total_coupon_amount;
        } else {
            $total_delivery_amount = $row->discount_type == "extra" ?
                $row->total_delivery_amount + $row->total_discount_amount : $row->total_delivery_amount - $row->total_discount_amount;
        }
        $leave_time = null;
        if($row->delivered_date){
            $startDay = \Carbon\Carbon::parse($row->pickup->pickup_date);
            $endDay = \Carbon\Carbon::parse($row->delivered_date);
            // $startTime = \Carbon\Carbon::parse($row->pickup->pickup_date->format('H:i:s'));
            // $endTime = \Carbon\Carbon::parse($row->delivered_date->format('H:i:s'));
         
            // $totalDuration = $endTime->diffForHumans($startTime);
            // $day = $startDay->diffInDays($endDay);
            $total_hours =  $startDay->diffInHours($endDay);
            $total_day = number_format((float)$total_hours/24, 2, '.', '');

            $leave_time = $total_day;
        }
        return [
            $row->voucher_invoice,
            $row->receiver->name,
            $row->receiver->phone,
            $row->receiver_city->name,
            $row->receiver_zone->name,
            // optional(optional($row->pickup)->pickuped_date)->format('Y-m-d'),
            $row->pickup_id ? ($row->pickup->pickup_date ?  $row->pickup->pickup_date->format('Y-m-d') : '') : '',
            optional($row->delivered_date)->format('Y-m-d'),
            $leave_time,
            $total_delivery_amount,
            optional($row->payment_type)->name,
            optional($row->delivery_status)->status,
            $row->total_item_price
        ];
    }
   
    public function headings(): array
    {
        return [
            'Order No',
            'Customer',
            'Phone',
            'City',
            'Zone',
            'Pickuped Date',
            'Delivered Date',
            'Leave Time',
            'Delivery Fee',
            'Payment Type',
            'Delivery status',
            'Parcel Price'
        ];
    }
}
