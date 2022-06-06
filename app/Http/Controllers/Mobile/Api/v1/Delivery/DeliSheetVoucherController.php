<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Models\Voucher;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Delivery\DeliSheetVoucher\DeliSheetVoucherCollection;

class DeliSheetVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vouchers = Voucher::deliSheet(request()->get('date'))->get();
        
        return new DeliSheetVoucherCollection($vouchers->load([
            'pickup', 'pickup.sender', 'customer', 'receiver_city', 'receiver_zone', 'call_status',
            'delivery_status', 'store_status'
        ]));
    }
}
