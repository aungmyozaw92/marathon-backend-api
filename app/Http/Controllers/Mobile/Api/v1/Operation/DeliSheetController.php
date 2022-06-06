<?php

namespace App\Http\Controllers\Mobile\Api\v1\Operation;

use App\Models\Voucher;
use App\Models\DeliSheet;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use App\Models\DeliSheetVoucher;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Operation\AssignVoucherRequest;
use App\Http\Requests\Mobile\Operation\RemoveVoucherRequest;
use App\Http\Resources\Mobile\Operation\Voucher\VoucherResource;
use App\Repositories\Mobile\Api\v1\Operation\DeliSheetRepository;
use App\Http\Resources\Mobile\Operation\DeliSheet\DeliSheetResource;
use App\Http\Resources\Mobile\Operation\DeliSheet\DeliSheetCollection;
use App\Http\Requests\Mobile\Operation\DeliSheet\CreateDeliSheetRequest;

class DeliSheetController extends Controller
{
    /**
     * @var DeliSheetRepository
     */
    protected $deliSheetRepository;

    /**
     * BusStationController constructor.
     *
     * @param DeliSheetRepository $deliSheetRepository
     */
    public function __construct(DeliSheetRepository $deliSheetRepository)
    {
        $this->deliSheetRepository = $deliSheetRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deliSheets = DeliSheet::filter(request()->only([
                        'date', 'start_date', 'end_date', 'delivery_id'
                    ]))
                    ->with('zone', 'delivery', 'staff')
                    ->where('is_closed', 0)
                    ->where('is_scanned', false)
                    ->whereHas('created_by', function($q){
                        $q->where('city_id', auth()->user()->city_id);
                    })
                    ->orderBy('id', 'desc')
                    ->paginate(20);

        return new DeliSheetCollection($deliSheets);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(CreateDeliSheetRequest $request)
    {
        $deliSheet = $this->deliSheetRepository->create($request->all());

        return new DeliSheetResource($deliSheet->load(['zone', 'delivery', 'staff']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assign_voucher(AssignVoucherRequest $request, DeliSheet $deliSheet)
    {
        if ($deliSheet->is_closed) {
            return response()->json(
                ['status' => 2,'message' => 'Delisheet is already closed.'],
                Response::HTTP_OK
            );
        }
        $voucher_no = request()->only(['voucher_no']);
        if ($voucher_no['voucher_no'][0] == 'V') {
            $voucher = Voucher::where('voucher_invoice', $voucher_no['voucher_no'])
                            ->firstOrFail();
        }else{
            $voucher = Voucher::where('id', $voucher_no['voucher_no'])->firstOrFail();
        }

        if ($voucher->delivery_status_id == 9) {
            return response()->json(
                ['status' => 2,'message' => 'Voucher is already returned.'],
                Response::HTTP_OK
            );
        }
        if ($voucher->delivery_status_id == 8) {
            return response()->json(
                ['status' => 2,'message' => 'Voucher is already delivered.'],
                Response::HTTP_OK
            );
        }
        if (!$voucher->pickup_id) {
            return response()->json(
                ['status' => 2,'message' => 'Cannot add coz this voucher is draft'],
                Response::HTTP_OK
            );
        }

        if ($voucher->origin_city_id != $voucher->receiver_city_id) {
            return response()->json(
                ['status' => 2,'message' => 'Cannot assign this voucher bcoz of waybill voucher'],
                Response::HTTP_OK
            );
        }

        $data['voucher_no'] = $voucher->voucher_invoice;
        $deliSheet = $this->deliSheetRepository->assign_voucher($deliSheet, $data);
        $voucher = array_key_exists('voucher', $deliSheet) ? $deliSheet['voucher'] : null;

        // return response()->json($deliSheet, Response::HTTP_OK);
        if ($voucher) {
            return new VoucherResource($voucher->load([
                'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'call_status',
                'delivery_status', 'store_status', 'parcels', 'pickup', 'pickup.sender', 'pickup.opened_by_staff' => function ($query) {
                    $query->withTrashed();
                }
            ]));
        } else {
            return response()->json($deliSheet, Response::HTTP_OK);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BusStation  $busStation
     * @return \Illuminate\Http\Response
     */
    public function show(Delisheet $deliSheet)
    {
        // return new DelisheetResource($deliSheet->load(['zone', 'delivery', 'staff','vouchers']));
        return new DeliSheetResource($deliSheet->load([
            'zone', 'delivery', 'staff', 'vouchers', 'vouchers.pickup', 'vouchers.pickup.sender',
            'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone', 'vouchers.call_status',
            'vouchers.delivery_status', 'vouchers.store_status', 'vouchers.payment_type', 'vouchers.attachments',
            'vouchers.pickup.sender.staff'
        ]));
    }

    /**
     * Remove Voucher resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function removeVouchers(RemoveVoucherRequest $request, DeliSheet $deliSheet)
    {

        if (!$deliSheet->is_closed) {
            $voucher = Voucher::whereVoucherInvoice($request->get('voucher_no'))->firstOrFail();
            $voucher_exists = DeliSheetVoucher::where('voucher_id',$voucher->id)->where('delisheet_id',$deliSheet->id)->exists();

           // $voucher_exists = DeliSheetVoucher::whereVoucherId($request->get('voucher_id'))->exists();

            if ($voucher_exists) {
                $deliSheet = $this->deliSheetRepository->remove_vouchers($deliSheet, $request->all());

                return response()->json([
                    'status' => 1, 'message' => 'Voucher has been successfully removed.'
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => 2, 'message' => 'Cannot remove because voucher does not remove in delisheet'
                ], Response::HTTP_OK);
            }
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot remove because deliSheet is already closed'
        ], Response::HTTP_OK);
    }


    public function confirm_scan(DeliSheet $deliSheet)
    {
        $deliSheet->is_scanned = true;
        $deliSheet->save();

        return new DeliSheetResource($deliSheet->load(['zone', 'delivery', 'staff']));
    }
}
