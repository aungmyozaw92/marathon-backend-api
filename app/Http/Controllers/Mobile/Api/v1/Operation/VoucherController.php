<?php

namespace App\Http\Controllers\Mobile\Api\v1\Operation;

use App\Models\Voucher;
use Illuminate\Http\Response;
use App\Models\WaybillVoucher;
use App\Models\DeliSheetVoucher;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Voucher\CreateVoucherRequest;
use App\Http\Requests\Mobile\Voucher\UpdateVoucherRequest;
use App\Http\Requests\Mobile\Operation\RemoveVoucherRequest;
use App\Repositories\Mobile\Api\v1\Operation\VoucherRepository;
use App\Http\Resources\Mobile\Operation\Voucher\VoucherResource;

class VoucherController extends Controller
{
    protected $voucherRepository;

    public function __construct(VoucherRepository $voucherRepository)
    {
        $this->voucherRepository = $voucherRepository;
    }

    public function index()
    {
        $vouchers = Voucher::filter(request()->only([
            'search'
        ]))
            ->whereHas('pickup', function ($q) {
                $q->where('sender_type', 'Merchant')
                    ->where('opened_by', auth()->user()->id);
            })
            ->orWhere(function ($q) {
                $q->where('created_by_id', auth()->user()->id)
                    ->where('created_by_type', 'Merchant');
            })
            ->with(['customer' => function ($query) {
                $query->withTrashed();
            }])
            ->orderBy('id', 'desc')->get();
        return new VoucherCollection($vouchers);
    }

    public function store(CreateVoucherRequest $request)
    {
        $response = $this->voucherRepository->create($request->all());

        if ($response['status'] === 1) {
            return new VoucherResource($response['data']->load([
                'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
                'call_status', 'delivery_status', 'store_status' => function ($query) {
                    $query->withTrashed();
                }
            ]));
        } else {
            return response()->json(['status' => $response['status'], 'message' => $response['message']]);
        }
        // if ($voucher) {
        //     return new VoucherResource($voucher->load([
        //         'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
        //         'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
        //         'call_status', 'delivery_status', 'store_status' => function ($query) {
        //             $query->withTrashed();
        //         }
        //     ]));
        // } else {
        //     return response()->json(['status' => 5, 'message' => 'Data integraty check again!']);
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Voucher  $pickup
     * @return \Illuminate\Http\Response
     */
    public function show(Voucher $voucher)
    {
        return new VoucherResource($voucher->load([
            'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'call_status',
            'delivery_status', 'store_status', 'parcels', 'pickup', 'pickup.sender', 'pickup.opened_by_staff' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    public function update(UpdateVoucherRequest $request, Voucher $voucher)
    {
        $voucher = $this->voucherRepository->update($voucher, $request->all());

        return new VoucherResource($voucher->load([
            'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
            'call_status', 'delivery_status', 'store_status' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    public function change_store_status(Voucher $voucher)
    {
        $voucher = $this->voucherRepository->change_store_status($voucher);

        if ($voucher) {
            return response()->json([
                'status' => 1,
                'message' => "Success!"
            ], Response::HTTP_OK);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Voucher $voucher)
    {
        $this->voucherRepository->destroy($voucher);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }

    public function scan_remove_voucher(RemoveVoucherRequest $request)
    {
        $voucher = Voucher::whereVoucherInvoice($request->get('voucher_no'))->firstOrFail();

        $deli_voucher = DeliSheetVoucher::where('voucher_id', $voucher->id)->latest()->first();
        $waybill_voucher = WaybillVoucher::where('voucher_id', $voucher->id)->latest()->first();

        if ($deli_voucher || $waybill_voucher) {
            if ($deli_voucher) {
                $deliSheet = $deli_voucher->deli_sheet;
                if (!$deliSheet->is_closed) {
                    $this->remove_voucher($deliSheet, $voucher, 'Delisheet');
                    return response()->json([
                        'status' => 1, 'message' => 'Voucher has been successfully removed.'
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'status' => 2, 'message' => 'Cannot remove because deliSheet is already closed'
                    ], Response::HTTP_OK);
                }
            }
            if ($waybill_voucher) {
                $waybill = $waybill_voucher->waybill;
                if (!$waybill->is_closed) {
                    $this->remove_voucher($waybill, $voucher, 'Waybill');
                    return response()->json([
                        'status' => 1, 'message' => 'Voucher has been successfully removed.'
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'status' => 2, 'message' => 'Cannot remove because Waybill is already closed'
                    ], Response::HTTP_OK);
                }
            }
        } else {
            return response()->json([
                'status' => 2, 'message' => 'Cannot remove because voucher does not exit in delisheet or waybill'
            ], Response::HTTP_OK);
        }
    }

    public function remove_voucher($delisheet_waybill, $voucher, $type)
    {
        if ($type == 'Delisheet') {
            $qty = $delisheet_waybill->qty;
            $deleted = $delisheet_waybill->vouchers()->detach($voucher->id);

            if ($deleted) {
                $voucher->outgoing_status = null;
                $voucher->store_status_id = 4;
                // $voucher->delivery_counter -= 1;
                $qty -= 1;
            }

            $delisheet_waybill->qty = $qty;

            if ($voucher->isDirty()) {
                $voucher->updated_by = auth()->user()->id;
                $voucher->save();
                $voucher->voucherSheetFire($delisheet_waybill->delisheet_invoice, 'remove_delisheet_voucher');
            }

            if ($delisheet_waybill->isDirty()) {
                $delisheet_waybill->updated_by = auth()->user()->id;
                $delisheet_waybill->save();
                $delisheet_waybill->delisheetVoucherFire($voucher->voucher_invoice, 'remove_delisheet_voucher');
            }
            return $delisheet_waybill->refresh();
        } else {
            $deleted = $delisheet_waybill->vouchers()->detach($voucher->id);

            if ($deleted) {
                $voucher->outgoing_status = null;
                $voucher->store_status_id = 4;
                // $voucher->delivery_counter -= 1;
            }

            if ($voucher->isDirty()) {
                $voucher->updated_by = auth()->user()->id;
                $voucher->save();
            }
            $voucher->voucherSheetFire($delisheet_waybill->waybill_invoice, 'remove_waybill_voucher');
            if ($delisheet_waybill->isDirty()) {
                $delisheet_waybill->updated_by = auth()->user()->id;
                $delisheet_waybill->save();
            }
            $delisheet_waybill->waybillVoucherFire($voucher->voucher_invoice, 'remove_waybill_voucher');
            return $delisheet_waybill->refresh();
        }
    }
}
