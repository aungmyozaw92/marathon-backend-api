<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Models\Staff;
use App\Models\Voucher;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Delivery\DeliSheet\DeliSheetCollection;
use App\Http\Resources\Mobile\Delivery\DeliSheetVoucher\DeliSheetVoucherCollection;

class DeliSheetController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\DeliSheet  $deliSheet
     * @return \Illuminate\Http\Response
     */
    public function delivery()
    {
        $delivery = Staff::findOrFail(auth()->user()->id);
        return new DeliSheetCollection($delivery->deli_sheets->load([
           'vouchers', 'vouchers.pickup' , 'vouchers.pickup.sender',
            'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone','vouchers.payment_type'
        ]));
    }
}
