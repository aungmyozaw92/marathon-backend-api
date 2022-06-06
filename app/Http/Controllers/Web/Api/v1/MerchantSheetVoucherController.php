<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Voucher;
use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantSheetVoucher\MerchantSheetVoucherCollection;

class MerchantSheetVoucherController extends Controller
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
                'receiver_gate',
                'sender_city',
                'sender_zone',
                'sender_bus_station',
                'sender_gate',
                'payment_type'
            )
                ->merchantSheet(request()->get('merchant_id'))
                ->filter(request()->only([
                    'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                    'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'pickup_invoice'
                ]))->paginate(25);

            return new MerchantSheetVoucherCollection($vouchers);
        }

        $vouchers = Voucher::merchantSheet(request()->get('merchant_id'))->get();

        return new MerchantSheetVoucherCollection($vouchers->load([
            'pickup', 'pickup.sender', 'customer', 'receiver_city', 'receiver_zone', 'call_status',
            'delivery_status', 'store_status', 'receiver_bus_station', 'receiver_gate', 'sender_city', 'sender_zone',
            'sender_bus_station', 'sender_gate', 'payment_type'
        ]));
    }
}
