<?php

namespace App\Http\Controllers\Mobile\Api\v1\Agent;

use App\Models\Voucher;
use App\Models\Waybill;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Agent\Waybill\ReceivedRequest;
use App\Http\Resources\Mobile\Agent\Waybill\WaybillResource;
use App\Http\Resources\Mobile\Agent\Waybill\WaybillCollection;

class WaybillController extends Controller
{
    public function index()
    {
        $waybills = Waybill::with(['delivery', 'staff', 'city', 'from_city', 'to_city',
        'to_bus_station', 'gate'])->receivedFilter(request()->only([
            'is_received']))->paginate(20);

        return new WaybillCollection($waybills);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Waybill  $waybill
     * @return \Illuminate\Http\Response
     */
    public function show(Waybill $waybill)
    {
        return new WaybillResource($waybill->load([
            'delivery', 'staff', 'from_city', 'to_city',
            'from_bus_station', 'to_bus_station', 'gate', 'vouchers', 'vouchers.pickup' , 'vouchers.pickup.sender',
            'vouchers.pickup.sender.staff','vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone',
            'vouchers.sender_bus_station', 'vouchers.receiver_bus_station', 'vouchers.sender_gate', 'vouchers.receiver_gate', 'vouchers.call_status',
            'vouchers.delivery_status', 'vouchers.store_status', 'vouchers.payment_type', 'city','attachments'
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Waybill  $waybill
     * @return \Illuminate\Http\Response
     */
    public function update(ReceivedRequest $request, Waybill $waybill)
    {

        $voucherIds = $waybill->vouchers->where('delivery_status_id', '!=', 9)->pluck('id')->toArray();

        $waybill_vouchers =  Voucher::whereIn('id', $voucherIds)->update(
            [
                'origin_city_id' => $waybill->to_city_id,
                'from_agent_id' => $waybill->from_agent_id,
                'to_agent_id' => $waybill->to_agent_id
            ]
        );

        if (!$waybill->is_received && $waybill->is_delivered && $waybill->is_confirm) {
            $waybill->is_received = $request->get('is_received');
            $waybill->received_date = now();
            $waybill->received_by_type = 'Agent';
            $waybill->received_by_id = auth()->user()->id;
            $waybill->save();
            return new WaybillResource($waybill->load(['delivery', 'staff', 'city', 'from_city', 'to_city', 
            'to_bus_station', 'gate', 'attachments']));
        }

        return response()->json([
            'status' => 2, 'message' => 'Waybill is already received, need to confirm or delivered'
        ], Response::HTTP_OK);
    }
}
