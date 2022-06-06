<?php

namespace App\Exports;

use App\Models\Journal;
use App\Models\Voucher;
use App\Models\Merchant;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class AgentWaybillVoucherSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
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
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration','from_agent','to_agent',
            'delegate_person', 'parcels' => function ($query) {
                $query->withTrashed();
            }
        ])->where('origin_city_id', $city_id)
            ->orWhere('sender_city_id', $city_id)
            ->filter(request()->all())
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
        return 'PartnerWaybillVoucher';
    }

    public function map($row): array
    {
        if ($row->outgoing_status === 0) {
            $row->assign_sheet = $row->delisheets()->latest()->first();
        } elseif ($row->outgoing_status === 1) {
            $row->assign_sheet = $row->waybills()->latest()->first();
        } elseif ($row->outgoing_status === 2) {
            $row->assign_sheet = $row->bussheets()->latest()->first();
        } elseif ($row->outgoing_status === 3) {
            $row->assign_sheet = "Merchant Sheet Draft";
        } elseif ($row->outgoing_status === 4) {
            $row->assign_sheet = $row->merchant_sheets()->latest()->first();
        } elseif ($row->outgoing_status === 5) {
            $row->assign_sheet = $row->return_sheets()->latest()->first();
        }
        //$assign_sheet = $assign_sheet;
        return [
            $row->voucher_invoice,
            $row->assign_sheet['waybill_invoice'],
            optional(optional($row->pickup)->sender)->name,
            $row->pickup->sender_type === 'Customer' ?  optional(optional($row->pickup)->sender)->phone :  implode(", ", optional(optional($row->pickup)->sender_associate)->phone),
            optional($row->receiver)->name,
            optional($row->receiver_city)->name,
            optional($row->from_agent)->name,
            optional($row->to_agent)->name,
            $row->sender_amount_to_collect,
            $row->receiver_amount_to_collect,
            ($row->discount_type == 'extra') ? 
                             $row->total_delivery_amount + $row->total_discount_amount : $row->total_delivery_amount - $row->total_discount_amount,
            optional($row->payment_type)->name,
            optional($row->delivery_status)->status,
            optional($row->messages->first())->message_text,
            $row->created_at,
            $row->end_date,
        ];
    }
   
    public function headings(): array
    {
        return [

            'Voucher No',
            'Waybill No',
            'Sender',
            'Sender Phone',
            'Receiver',
            'To City',
            'From Agent',
            'To Agent',
            'ATC Sender',
            'ATC Recipient',
            'Service Fee',
            'Payment Type',
            'Delivery Status',
            'Can\'t Deliver Reason/Message',
            'Created Date',
            'End Date'
        ];
    }
}
