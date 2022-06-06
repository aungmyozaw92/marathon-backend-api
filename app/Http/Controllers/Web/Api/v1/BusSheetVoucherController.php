<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Voucher;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusSheetVoucher\BusSheetVoucherCollection;

class BusSheetVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('paginate')) {
            $vouchers = Voucher::with(
                'pickup',
                'pickup.sender',
                'customer',
                'receiver_city',
                'receiver_zone',
                'call_status',
                'delivery_status',
                'store_status',
                'receiver_bus_station',
                'sender_bus_station',
                'receiver_gate',
                'sender_gate',
                'sender_city',
                'sender_zone',
                'sender_bus_station',
                'sender_gate',
                'payment_type'
            )
                ->busSheet()
                ->filter(request()->only([
                    'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                    'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'sender_gate',
                    'payment_type'
                ]))->paginate(25);

            return new BusSheetVoucherCollection($vouchers);
        }
        $vouchers = Voucher::busSheet(request()->get('from_bus_station'))->get();

        return new BusSheetVoucherCollection($vouchers->load([
            'pickup', 'pickup.sender', 'customer', 'receiver_city', 'receiver_zone', 'call_status',
            'delivery_status', 'store_status', 'receiver_bus_station', 'sender_bus_station', 'receiver_gate', 'sender_gate', 'sender_city', 'sender_zone',
            'sender_bus_station', 'sender_gate', 'payment_type'
        ]));
    }
}
