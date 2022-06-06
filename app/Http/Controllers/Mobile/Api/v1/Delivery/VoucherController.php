<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Models\Staff;
use App\Models\Pickup;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Voucher\CreateVoucherRequest;
use App\Http\Requests\Mobile\Voucher\UpdateVoucherRequest;
use App\Repositories\Mobile\Api\v1\Delivery\VoucherRepository;
use App\Http\Resources\Mobile\Delivery\Voucher\VoucherResource;
use App\Http\Resources\Mobile\Delivery\Voucher\VoucherCollection;
use App\Http\Requests\Mobile\Delivery\Voucher\UpdateVoucherStatusRequest;

//use App\Http\Requests\Voucher\UpdateVoucherRequest;

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
                    ->where('pickuped_by_id', auth()->user()->id);
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
        $voucher = $this->voucherRepository->create($request->all());
        if ($voucher) {
            return new VoucherResource($voucher->load([
                'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
                'call_status', 'delivery_status', 'store_status' => function ($query) {
                    $query->withTrashed();
                }
            ]));
        } else {
            return response()->json(['status' => 5, 'message' => 'Data integraty check again!']);
        }
    }

    public function show(Voucher $voucher)
    {
        $delivery = Staff::findOrFail(auth()->user()->id);
        $deli_voucherIds = $delivery->deli_sheets()->with('deli_sheet_vouchers')->get()->pluck('deli_sheet_vouchers')->collapse()
                                     ->where('voucher_id', $voucher->id)
                                     
                                     ->where('delivery_status', 0)
                                     ->where('return', 0)
                                    ->pluck('voucher_id')->toArray();
        if ($deli_voucherIds) {
            return new VoucherResource($voucher->load([
                'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'call_status', 'delivery_status',
                'store_status', 'parcels', 'pickup', 'pickup.sender', 'pickup.sender.staff', 'pickup.opened_by_staff', 'attachments' => function ($query) {
                    $query->withTrashed();
                }
            ]));
        } else {
            return response()->json(['status' => 2,'message' => 'Please select related voucher.'], Response::HTTP_OK);
        }
    }

    public function scan_voucher($voucher_no)
    {
        $voucher = Voucher::where('voucher_invoice',$voucher_no)->firstOrFail();
        $delivery = Staff::findOrFail(auth()->user()->id);
        $deli_voucherIds = $delivery->deli_sheets()->with('deli_sheet_vouchers')->get()->pluck('deli_sheet_vouchers')->collapse()
                                     ->where('voucher_id', $voucher->id)
                                     
                                     ->where('delivery_status', 0)
                                     ->where('return', 0)
                                    ->pluck('voucher_id')->toArray();
        if ($deli_voucherIds) {
            return new VoucherResource($voucher->load([
                'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'call_status', 'delivery_status',
                'store_status', 'parcels', 'pickup', 'pickup.sender', 'pickup.sender.staff', 'pickup.opened_by_staff', 'attachments' => function ($query) {
                    $query->withTrashed();
                }
            ]));
        } else {
            return response()->json(['status' => 2,'message' => 'Please select related voucher.'], Response::HTTP_OK);
        }
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

    public function update_status(UpdateVoucherStatusRequest $request, Voucher $voucher)
    {
        $voucher = $this->voucherRepository->update_status($voucher, $request->all());

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
