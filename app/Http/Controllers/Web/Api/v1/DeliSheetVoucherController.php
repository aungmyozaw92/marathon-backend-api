<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Voucher;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeliSheetVoucher\DeliSheetVoucherCollection;

class DeliSheetVoucherController extends Controller
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
                'store_status'
            )
                ->deliSheet()
                ->filter(request()->only([
                    'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                    'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'thirdparty_invoice'
                ]))->paginate(25);

            return new DeliSheetVoucherCollection($vouchers);
        }
        $vouchers = Voucher::deliSheet()->get();

        return new DeliSheetVoucherCollection($vouchers->load([
            'pickup', 'pickup.sender', 'customer', 'receiver_city', 'receiver_zone', 'call_status',
            'delivery_status', 'store_status'
        ]));
    }
}
