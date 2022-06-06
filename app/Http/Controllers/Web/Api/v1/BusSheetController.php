<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Voucher;
use App\Models\BusSheet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\BusSheetVoucher;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusSheet\BusSheetResource;
use App\Http\Requests\DeliSheet\AddVoucherRequest;
use App\Http\Resources\BusSheet\BusSheetCollection;
use App\Repositories\Web\Api\v1\BusSheetRepository;
use App\Http\Requests\BusSheet\CreateBusSheetRequest;
use App\Http\Requests\BusSheet\UpdateBusSheetRequest;
use App\Http\Requests\DeliSheet\RemoveVoucherRequest;

class BusSheetController extends Controller
{
    /**
     * @var BusSheetRepository
     */
    protected $busSheetRepository;

    /**
     * BusSheetController constructor.
     *
     * @param BusSheetRepository $busSheetRepository
     */
    public function __construct(BusSheetRepository $busSheetRepository)
    {
        $this->busSheetRepository = $busSheetRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $busSheets = BusSheet::filter(request()->only([
                            'date', 'start_date', 'end_date', 'delivery_id'
                        ]))
                        ->whereHas('vouchers', function ($query) {
                            $query->where('origin_city_id', auth()->user()->city_id);
                        })
                        ->get();

        return new BusSheetCollection($busSheets->load(['vouchers', 'delivery', 'staff']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBusSheetRequest $request)
    {
        if ($request->get('vouchers')) {
            $request->merge([
                'vouchers' => array_unique($request->get('vouchers'), SORT_REGULAR)
            ]);

            foreach ($request->get('vouchers') as $voucher) {
                $voucher_exists = BusSheetVoucher::whereVoucherId($voucher['id'])->exists();
                $voucher = Voucher::findOrFail($voucher['id']);
                if ($voucher_exists && !is_null($voucher->outgoing_status)) {
                    return response()->json([
                        'status' => 4, 'message' => 'Voucher is already assigned to BusSheet.'
                    ], Response::HTTP_OK);
                }
            }
        }

        $busSheet = $this->busSheetRepository->create($request->all());

        return new BusSheetResource($busSheet->load(['vouchers', 'delivery', 'staff']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BusSheet  $busSheet
     * @return \Illuminate\Http\Response
     */
    public function show(BusSheet $busSheet)
    {
        return new BusSheetResource($busSheet->load([
            'delivery', 'staff', 'vouchers', 'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone', 'vouchers.call_status',
            'vouchers.delivery_status', 'vouchers.store_status', 'vouchers.payment_type', 'vouchers.receiver_bus_station', 'from_bus_station',
            'vouchers.receiver_gate', 'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.sender_bus_station', 'vouchers.sender_gate',
            'vouchers.attachments'
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BusSheet  $busSheet
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBusSheetRequest $request, BusSheet $busSheet)
    {
        if (!$busSheet->is_closed) {
            $busSheet = $this->busSheetRepository->update($busSheet, $request->all());

            return new BusSheetResource($busSheet->load(['vouchers', 'delivery', 'staff']));
        }

        return response()->json([
            'status' => 2, 'message' => 'BusSheet is already closed.'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BusSheet  $busSheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusSheet $busSheet)
    {
        $this->busSheetRepository->destroy($busSheet);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DeliSheet  $deliSheet
     * @return \Illuminate\Http\Response
     */
    public function delivery(Staff $delivery)
    {
        return new BusSheetCollection($delivery->bus_sheets->load([
            'delivery', 'staff', 'vouchers', 'vouchers.pickup', 'vouchers.pickup.sender',
            'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone', 'vouchers.call_status',
            'vouchers.delivery_status', 'vouchers.store_status', 'vouchers.payment_type'
        ]));
    }

    public function removeVouchers(RemoveVoucherRequest $request, BusSheet $busSheet)
    {
        if (!$busSheet->is_closed) {
            $busSheet = $this->busSheetRepository->remove_vouchers($busSheet, $request->all());

            return response()->json([
                'status' => 1, 'message' => 'Ok successful.'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot remove because deliSheet is already closed'
        ], Response::HTTP_OK);
    }

    public function addVouchers(AddVoucherRequest $request, BusSheet $busSheet)
    {
        if (!$busSheet->is_closed) {
            $busSheet = $this->busSheetRepository->add_vouchers($busSheet, $request->all());

            return response()->json([
                'status' => 1, 'message' => 'Voucher has been successfully added.'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot add new voucher because busSheet is already closed'
        ], Response::HTTP_OK);
    }
}
