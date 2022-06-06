<?php

namespace App\Http\Controllers\Mobile\Api\v1\Operation;

use App\Models\Waybill;
use Illuminate\Http\Response;

use App\Models\WaybillVoucher;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Operation\AssignVoucherRequest;
use App\Http\Requests\Mobile\Operation\RemoveVoucherRequest;
use App\Repositories\Mobile\Api\v1\Operation\WaybillRepository;
use App\Http\Resources\Mobile\Operation\Voucher\VoucherResource;
use App\Http\Resources\Mobile\Operation\Waybill\WaybillResource;
use App\Http\Resources\Mobile\Operation\Waybill\WaybillCollection;
use App\Http\Requests\Mobile\Operation\RemoveWaybillVoucherRequest;

class WaybillController extends Controller
{
    /**
     * @var WaybillRepository
     */
    protected $waybillRepository;

    /**
     * BusStationController constructor.
     *
     * @param WaybillRepository $waybillRepository
     */
    public function __construct(WaybillRepository $waybillRepository)
    {
        $this->waybillRepository = $waybillRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $waybills = Waybill::filter(request()->only(['date', 'start_date', 'end_date', 'delivery_id']))
                ->with([
                    'delivery', 'staff', 'city', 'from_city', 'to_city',
                    'to_bus_station', 'gate', 'attachments'])
                ->where('is_closed', 0)
                ->where('is_scanned', false)
                ->where('from_city_id', auth()->user()->city_id)
               // ->select('waybills.*', \DB::raw('(SELECT name FROM cities WHERE waybills.to_city_id = cities.id ) as name'))
                ->orderBy('created_at','desc')
                ->paginate(20);

        return new WaybillCollection($waybills);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assign_voucher(AssignVoucherRequest $request, Waybill $waybill)
    {
        if ($waybill->is_closed || $waybill->is_received || $waybill->is_confirm) {
            return response()->json(
                ['status' => 2,'message' => 'Cannot add new voucher because waybill is already closed and receiced'],
                Response::HTTP_OK
            );
        }
        
        $waybill = $this->waybillRepository->assign_voucher($waybill, $request->all());
        $voucher = array_key_exists('voucher', $waybill) ? $waybill['voucher'] : null;

        // return response()->json($waybill, Response::HTTP_OK);
        if ($voucher) {
            return new VoucherResource($voucher->load([
                'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'call_status',
                'delivery_status', 'store_status', 'parcels', 'pickup', 'pickup.sender', 'pickup.opened_by_staff' => function ($query) {
                    $query->withTrashed();
                }
            ]));
        } else {
            return response()->json($waybill, Response::HTTP_OK);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BusStation  $busStation
     * @return \Illuminate\Http\Response
     */
    public function show(Waybill $waybill)
    {
        return new WaybillResource($waybill->load([
            'from_bus_station', 'to_bus_station', 'gate', 'from_city', 'to_city', 'delivery', 'staff', 'vouchers',
            'vouchers.pickup', 'vouchers.pickup.sender', 'vouchers.customer', 'vouchers.receiver_city',
            'vouchers.receiver_zone', 'vouchers.call_status', 'vouchers.delivery_status', 'vouchers.store_status',
            'vouchers.payment_type', 'city', 'attachments', 'vouchers.pickup.sender.staff'
        ]));
    }

    public function removeVouchers(RemoveWaybillVoucherRequest $request, Waybill $waybill)
    {
        if ((!$waybill->is_closed || !$waybill->is_received) && !$waybill->is_confirm) {
            $voucher_exists = WaybillVoucher::where('voucher_id', $request->get('voucher_id'))->exists();

            if ($voucher_exists) {
                $waybill = $this->waybillRepository->remove_vouchers($waybill, $request->all());

                return response()->json([
                    'status' => 1, 'message' => 'Voucher has been successfully removed.'
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => 2, 'message' => 'Cannot remove because voucher does not remove in waybill'
                ], Response::HTTP_OK);
            }
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot remove because waybill is already closed or received'
        ], Response::HTTP_OK);
    }

    public function confirm_scan(Waybill $waybill)
    {
        $waybill->is_scanned = true;
        $waybill->save();

        return new WaybillResource($waybill->load([
            'from_bus_station', 'to_bus_station', 'gate', 'from_city', 'to_city', 'delivery', 'staff', 'city', 'attachments'
        ]));
    }

    public function confirm_waybill(Waybill $waybill)
    {
        if ($waybill->is_closed || $waybill->is_received || $waybill->is_confirm) {
            return response()->json(
                ['status' => 2,'message' => 'Cannot confirm because waybill is already closed or receiced or confirm'],
                Response::HTTP_OK
            );
        }
        
        $waybill->is_confirm = true;
        $waybill->save();
        $waybill->refresh();

        return response()->json(
            ['status' => 1,'message' => 'Successcully confirm'],
            Response::HTTP_OK
        );
    }
}
