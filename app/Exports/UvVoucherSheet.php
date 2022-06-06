<?php

namespace App\Exports;

use App\Models\Voucher;
use App\Models\Merchant;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class UvVoucherSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        $agent_city_id = request()->get('agent_city_id');
        if ($agent_city_id) {
            $city_id = $agent_city_id;
        } else {
            $city_id = auth()->user()->city_id;
        }
        $vouchers = Voucher::with([
            'pickup', 'pickup.sender', 'customer',
            'messages', 'sender_city', 'receiver_city','receiver_zone',
             'delivery_status','store_status' => function ($query) {
                 $query->withTrashed();
             }
        ])->where('origin_city_id', $city_id)
            ->orWhere('sender_city_id', $city_id)
            ->filter(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'thirdparty_invoice',
                'outgoing_status', 'store_status', 'try_to_deliver', 'waybill_id', 'waybill_invoice',
                'sender_phone', 'sender_name', 'receiver_amount_to_collect', 'waybill_start_date',
                'waybill_end_date', 'voucher_type', 'postpone_date', 'postpone', 'pending_return'
            ]))
            ->whereNotNull('pickup_id')
            ->order(request()->only([
                'sortBy', 'orderBy'
            ]));

        if (request()->get('associated_merchant') === "true") {
            $merchants_id = Merchant::where('staff_id', auth()->user()->id)->get()->pluck('id');
            $vouchers = $vouchers->whereHas('pickup', function ($query) use ($merchants_id) {
                $query->where('sender_type', 'Merchant')
                    ->whereIn('sender_id', $merchants_id);
            });
        }
        return $vouchers->get();
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
        $receiver = $row->customer;
        if ($row->total_coupon_amount > 0) {
            $total_delivery_amount = $row->total_delivery_amount - $row->total_coupon_amount;
        } else {
            $total_delivery_amount = $row->discount_type == "extra" ?
                $row->total_delivery_amount + $row->total_discount_amount : $row->total_delivery_amount - $row->total_discount_amount;
        }
        $msg = "";
        foreach ($row->messages as $message) {
            $msg.=$message->message_text.' ';
        }
        return [
            $row->voucher_invoice,
            ($row->pickup->pickup_date) ? $row->pickup->pickup_date->format('Y-m-d') : '-',
            ($row->delivered_date) ? $row->delivered_date->format('Y-m-d') : '-',
            $msg,
            $row->sender_amount_to_collect ? $row->sender_amount_to_collect : '0',
            $row->receiver_amount_to_collect ? $row->receiver_amount_to_collect : '0',
            $row->delivery_status->status,
            $row->store_status->status,
            $row->pickup ? $row->pickup->sender->name : null,
            $receiver->name,
            $receiver->phone,
            $row->thirdparty_invoice,
            $row->receiver_city->name ,
            $row->receiver_zone->name,
            $total_delivery_amount,
            $row->returned_date,
        ];
    }

    public function headings(): array
    {
        return [
            'Voucher No',
            'Pickup Date',
            'Delivered Date',
            'Message',
            'ATC Sender',
            'ATC Receiver',
            'Delivery status',
            'Store status',
            'Sender Name',
            'Receiver Name',
            'Receiver Phone',
            'Thirdparty Invoice',
            'City',
            'Zone',
            'Service Fees',
            'Return Date'
        ];
    }
}
