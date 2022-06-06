<?php

namespace App\Exports;

use App\Models\Voucher;
use App\Models\Merchant;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class DraftVoucherExport implements FromCollection, WithTitle, WithHeadings, WithMapping
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
            'pickup', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration', 'delegate_person', 'parcels' => function ($query) {
                $query->withTrashed();
            }
        ])->where('origin_city_id', $city_id)
            ->orWhere('sender_city_id', $city_id)
            ->filterDraft(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'sender_name', 'sender_phone', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'thirdparty_invoice',
                'outgoing_status', 'store_status', 'try_to_deliver'
            ]))
            ->whereNull('pickup_id')
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
        return 'Agent';
    }

    public function map($row): array
    {
        $receiver = $row->receiver;
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
            ($row->delivered_date) ? $row->delivered_date : '-',
            $msg,
            $row->sender_amount_to_collect ? $row->sender_amount_to_collect : '0',
            $row->receiver_amount_to_collect ? $row->receiver_amount_to_collect : '0',
            $row->delivery_status->status,
            $row->created_by_merchant ? $row->created_by_merchant->name : null,
            $receiver->name,
            $receiver->phone,
            $row->thirdparty_invoice,
            $row->receiver_city->name . '-' . $row->receiver_zone->name,
            $total_delivery_amount,
        ];
    }

    public function headings(): array
    {
        return [
            'Voucher No',
            'Delivered Date',
            'Message',
            'ATC Sender',
            'ATC Receiver',
            'Delivery status',
            'Sender Name',
            'Receiver Name',
            'Receiver Phone',
            'Thirdparty Invoice',
            'City Zone',
            'Service Fees',
        ];
    }
}
