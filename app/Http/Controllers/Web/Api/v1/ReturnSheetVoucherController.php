<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Voucher;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReturnSheetVoucher\ReturnSheetVoucherCollection;

class ReturnSheetVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vouchers = Voucher::returnSheet(request()->get('merchant_id'))->get();
        // , 'parcels', 'pickup', 'pickup.sender', 'receiver_bus_station', 'receiver_gate',  'sender_bus_station', 'sender_gate', 
        return new ReturnSheetVoucherCollection($vouchers->load([
            'customer', 'receiver_city', 'receiver_zone', 'call_status', 'delivery_status', 
            'store_status', 'sender_city', 'sender_zone', 'payment_type','pending_returning_actor'
        ]));
    }
}
