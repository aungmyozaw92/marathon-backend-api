<?php

namespace App\Http\Controllers\Web\Api\v1\Customer;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerWeb\Voucher\VoucherResource;
use App\Http\Resources\CustomerWeb\Voucher\VoucherCollection;

class VoucherController extends Controller
{
    public function index()
    {
        $orderBy = (request()->has('orderBy'))? request()->get('orderBy') : 'desc';
        $vouchers = Voucher::where('receiver_id', auth()->user()->id)
                            ->filter(request()->only([
                                'start_date','end_date'
                            ]))
                            ->with('pickup', 'pickup.sender.staff', 'pickup.sender', 
                                   'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                                   'delivery_status', 'store_status', 'payment_status')
                            ->orderBy('id', $orderBy);

        if (request()->has('paginate')) {
            $vouchers = $vouchers->paginate(request()->get('paginate'));
        } else {
            $vouchers = $vouchers->get();
        }

        return new VoucherCollection($vouchers);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Voucher  $Voucher
     * @return \Illuminate\Http\Response
     */
    public function show(Voucher $voucher)
    {
        return new VoucherResource($voucher->load(['pickup', 'pickup.sender.staff', 'pickup.sender', 
        'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
        'delivery_status', 'store_status', 'payment_status','tracking_vouchers'
        ]));
    }
}
