<?php

namespace App\Http\Controllers\Mobile\Api\v1\Agent;

use App\Models\Voucher;
use App\Models\Waybill;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
        $user = auth()->user();
        
        // $incoming_waybill_count = Waybill::where('to_city_id', $user->city_id)->where('is_received', 0)->count();
       $incoming_waybill_count = Waybill::where('to_city_id', $user->city_id)
                                        ->where('to_agent_id', $user->id)
                                        ->where('is_received', 0)->count();
        $cant_deliver_count = Voucher::agentWaybillVoucher(['cant_deliver' => 1])->count();
        $deliver_count = Voucher::agentWaybillVoucher(['cant_deliver' => 0])->count();
        $delivered_count = Voucher::AgentWaybillVoucherListWithStatusId(['delivery_status_id' => 8])->count();
        $return_count = Voucher::AgentWaybillVoucherListWithStatusId(['delivery_status_id' => 9])->count();

        return response()->json([
            'status' => 1,
            'data' => [
                'incoming_waybill_count' => $incoming_waybill_count,
                'cant_deliver_count' => $cant_deliver_count,
                'deliver_count' => $deliver_count,
                'delivered_count' => $delivered_count,
                'return_count' => $return_count,
            ],
        ], 200);
        

    }

}
