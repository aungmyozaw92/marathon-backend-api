<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Voucher;
use App\Http\Controllers\Controller;
use App\Http\Resources\WayBillVoucher\WayBillVoucherCollection;

class WayBillVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $agent_city_id = request()->get('agent_city_id');
        // if ($agent_city_id) {
        //     $city_id = $agent_city_id;
        // } else {
        //     $city_id = auth()->user()->city_id;
        // }        

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
                ->wayBill(request()->only(['agent_id']))
                ->filter(request()->only([
                    'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                    'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'thirdparty_invoice'
                ]))
                ->where(function ($query) use ($agent_city_id){
                    if (auth()->user()->department->department === 'Agent') {
                        if ($agent_city_id) {
                            $query->where('origin_city_id',$agent_city_id)
                                    ->orWhere(function ($q) use ($agent_city_id){
                                        $q->where('delivery_status_id',9)
                                        ->where('receiver_city_id',$agent_city_id);
                                    });
                        }else{
                            $query;
                        }
                    }else{
                        $query->where(function ($query){
                            $query->where('origin_city_id', auth()->user()->city_id);
                            $query->where('delivery_status_id', '!=' ,9);
                        })->orWhere(function ($query){
                            $query->where('receiver_city_id', auth()->user()->city_id);
                            $query->where('delivery_status_id', '=' ,9);
                        });
                        
                    }
                    // auth()->user()->hasRole('Agent')  ? $agent_city_id? $query->where('origin_city_id',$agent_city_id) : $query 
                    //                         : $query->where('origin_city_id', auth()->user()->city_id);
                })
                
               // ->where('origin_city_id', $city_id)
                ->paginate(25);
            return new WayBillVoucherCollection($vouchers);
        }
        $vouchers = Voucher::wayBill(request()->get('date'))->where('origin_city_id', $city_id)->get();

        return new WayBillVoucherCollection($vouchers->load([
            'pickup', 'pickup.sender', 'customer', 'receiver_city', 'receiver_zone', 'call_status',
            'delivery_status', 'store_status', 'receiver_bus_station', 'receiver_gate', 'sender_city', 'sender_zone',
            'sender_bus_station', 'sender_gate', 'payment_type'
        ]));
    }
}
