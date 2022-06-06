<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\Voucher;
use App\Http\Controllers\Controller;
use App\Http\Resources\Voucher\VoucherResource;
use App\Repositories\Web\Api\v1\MerchantDashboard\IncompleteVoucherRepository;
use App\Http\Resources\MerchantDashboard\IncompleteVoucher\IncompleteVoucherResource;
use App\Http\Resources\MerchantDashboard\IncompleteVoucher\IncompleteVoucherCollection;
use App\Http\Requests\MerchantDashboard\IncompleteVoucher\UpdateIncompletVoucherRequest;
use App\Http\Requests\MerchantDashboard\IncompleteVoucher\CreateIncompleteVoucherRequest;
use App\Http\Requests\MerchantDashboard\IncompleteVoucher\UpdateReceiverIncompletVoucherRequest;

class IncompleteVoucherController extends Controller
{
    protected $incompleteVoucherRepository;

    public function __construct(IncompleteVoucherRepository $incompleteVoucherRepository)
    {
        $this->incompleteVoucherRepository = $incompleteVoucherRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vouchers = Voucher::with([
                    'pickup', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                    'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
                    'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration', 'delegate_person', 
                    'parcels','pending_returning_actor' => function ($query) {
                        $query->withTrashed();
                        }
                    ])
                        ->where('created_by_id', auth()->user()->id)
                        ->where('created_by_type', 'Merchant')
                        ->whereNull('pickup_id')
                        ->whereNull('receiver_id')
                        ->filterDraft(request()->only([
                            'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                            'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                            'start_date', 'end_date', 'from_city_id', 'to_city_id', 'pickup_date', 'thirdparty_invoice'
                        ]))
                        ->order(request()->only([
                            'sortBy', 'orderBy'
                        ]));

        if (request()->has('paginate')) {
            $vouchers = $vouchers->paginate(request()->get('paginate'));
        } else {
            $vouchers = $vouchers->get();
        }
        return new IncompleteVoucherCollection($vouchers);
    }

    public function store(CreateIncompleteVoucherRequest $request)
    {
        $request['platform'] = 'Merchant Dashboard';

        $response = $this->incompleteVoucherRepository->create($request->all());
        // dd($response);
        return new IncompleteVoucherResource($response->load([
                'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
                'call_status', 'delivery_status', 'store_status', 'parcels', 'pickup', 'pickup.sender', 'pickup.opened_by_staff' => function ($query) {
                    $query->withTrashed();
                }
            ]));
    }

    public function update(UpdateIncompletVoucherRequest $request, Voucher $voucher)
    {
        if ($voucher->is_closed || $voucher->is_picked) {
            return response()->json(['status' => 2, "message" => "Cannot update voucher is already closed or return"]);
        } else {
            $response = $this->incompleteVoucherRepository->update($voucher, $request->all());
            return new IncompleteVoucherResource($response['data']->load([
                'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
                'call_status', 'delivery_status', 'store_status', 'parcels', 
                'pickup', 'pickup.sender', 'pickup.opened_by_staff','pending_returning_actor' => function ($query) {
                    $query->withTrashed();
                }
            ]));
        }
    }

    public function update_receiver(UpdateReceiverIncompletVoucherRequest $request, Voucher $voucher)
    {
        if ($voucher->is_closed || $voucher->is_picked) {
            return response()->json(['status' => 2, "message" => "Cannot update voucher is already closed or return"]);
        } else {
            $response = $this->incompleteVoucherRepository->update_receiver($voucher, $request->all());
            return new IncompleteVoucherResource($response->load([
                'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
                'call_status', 'delivery_status', 'store_status', 'parcels', 
                'pickup', 'pickup.sender', 'pickup.opened_by_staff','pending_returning_actor' => function ($query) {
                    $query->withTrashed();
                }
            ]));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function show(Voucher $voucher)
    {
        return new IncompleteVoucherResource($voucher->load([
            'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
            'call_status', 'delivery_status', 'store_status', 'parcels', 
            'pickup', 'pickup.sender', 'pickup.opened_by_staff','pending_returning_actor' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }
}
