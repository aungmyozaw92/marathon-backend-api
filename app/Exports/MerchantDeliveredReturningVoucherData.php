<?php

namespace App\Exports;

use App\Models\Voucher;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class MerchantDeliveredReturningVoucherData implements FromCollection, WithTitle, WithHeadings, WithMapping
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
            ->where(function ($qr) {
                $qr->where('delivery_status_id', 8) //delivered vouchers
                    ->whereHas('pickup', function ($q) {
                        $q->where('sender_type', 'Merchant')
                            ->where('sender_id', auth()->user()->id);
                    });
            })
            ->orWhere(function ($qr) {
                $qr->where('is_return', false)
                    ->where('delivery_status_id', 9)
                    ->has('return_sheets', '<', 1) // not has return sheets
                    ->whereHas('pickup', function ($q) {
                        $q->where('sender_type', 'Merchant')
                            ->where('sender_id', auth()->user()->id);
                    });
                // ->whereHas('return_sheets', function ($q) {
                //     $q->where('is_returned', false);
                // });
            }) // returning vouchers
            ->filter(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'pickup_date', 'pickup_start_date',
                'pickup_end_date', 'delivered_start_date', 'delivered_end_date'
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
        return 'Delivered Returning Vouchers';
    }

    public function map($row): array
    {
        if ($row->total_coupon_amount > 0) {
            $total_delivery_amount = $row->total_delivery_amount - $row->total_coupon_amount;
        } else {
            $total_delivery_amount = $row->discount_type == "extra" ?
                $row->total_delivery_amount + $row->total_discount_amount : $row->total_delivery_amount - $row->total_discount_amount;
        }
       
        return [
            $row->voucher_invoice,
            $row->receiver->name,
            $row->receiver->phone,
            $row->receiver_city->name,
            $row->receiver_zone->name,
            optional(optional($row->pickup)->pickuped_date)->format('Y-m-d'),
            optional($row->delivered_date)->format('Y-m-d'),
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
            'Delivery Fee',
            'Payment Type',
            'Delivery status',
            'Parcel Price'
        ];
    }
}
