<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Models\Staff;
use App\Models\Voucher;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Delivery\Pickup\PickupCollection;
use App\Http\Resources\Mobile\Delivery\Voucher\VoucherCollection;

class FinanceVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getVouchers()
    {
        $delivery = Staff::findOrFail(auth()->user()->id);
        $deli_voucherIds = $delivery->deli_sheets()->where('is_paid', 0)->with('vouchers')->get()
                                    ->pluck('vouchers')->collapse()
                                    ->where('delivery_status_id', 8)
                                    ->pluck('id')->toArray();

        // $prepaid_voucherIds = $delivery->pickups()->with('vouchers')->get()
        //     ->pluck('vouchers')->collapse()
        //     ->whereIn('payment_type_id', [9, 10])->where('is_closed', 0)
        //     ->pluck('id')->toArray();

        // $voucherIds = array_merge($deli_voucherIds, $prepaid_voucherIds);
        $voucherIds = $deli_voucherIds;
        $vouchers = Voucher::with('customer')->whereIn('id', $voucherIds)->orderBy('id', 'desc')->get();

        return new VoucherCollection($vouchers->load([
            // 'pickup', 
            // 'pickup.sender', 
            'customer', 
            // 'payment_type', 
            //  'receiver_city', 
            // 'receiver_zone',
            // 'sender_bus_station', 
            // 'receiver_bus_station', 
            // 'sender_gate', 
            // 'receiver_gate',
            // 'call_status', 
            // 'delivery_status', 
            // 'store_status'
        ]));
    }

    public function getPickups()
    {
        $delivery = Staff::findOrFail(auth()->user()->id);

        $pickups = $delivery->pickuped_by_pickups()->where('is_pickuped', 1)
            ->where('is_paid', 0)->orderBy('id', 'desc')->get();

        return new PickupCollection($pickups->load(['sender', 'sender_associate'
        ]));
    }
}
