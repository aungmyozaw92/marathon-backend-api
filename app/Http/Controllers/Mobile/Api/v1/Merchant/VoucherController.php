<?php

namespace App\Http\Controllers\Mobile\Api\v1\Merchant;

use App\Models\City;
use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\QrAssociate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v1\QrRepository;
use App\Http\Requests\Mobile\Voucher\BindQrRequest;
use App\Repositories\Mobile\Api\v1\VoucherRepository;
use App\Http\Resources\Mobile\Voucher\VoucherResource;
use App\Http\Resources\Mobile\Voucher\VoucherCollection;
use App\Http\Requests\Mobile\Voucher\CreateVoucherRequest;
use App\Http\Requests\Mobile\Voucher\UpdateVoucherRequest;

//use App\Http\Requests\Voucher\UpdateVoucherRequest;

class VoucherController extends Controller
{
    protected $voucherRepository;

    public function __construct(
        VoucherRepository $voucherRepository,
        QrRepository $qrRepository
    ) {
        $this->voucherRepository = $voucherRepository;
        $this->qrRepository = $qrRepository;
    }

    public function index()
    {
        $vouchers = Voucher::filter(request()->only([
            'search'
        ]))
            ->whereHas('pickup', function ($q) {
                $q->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->orWhere(function ($q) {
                $q->where('created_by_id', auth()->user()->id)
                    ->where('created_by_type', 'Merchant');
            })
            ->with([
                'pickup', 'pickup.sender', 'customer', 'payment_type', 'attachments',
                'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
                'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration',
                'delegate_person', 'parcels','pending_returning_actor' => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->orderBy('id', 'desc')
            ->paginate(20);
        // $vouchers = Voucher::with('parcels')->paginate(20);

        return new VoucherCollection($vouchers);
        //return new VoucherCollection($vouchers);
    }

    public function store(CreateVoucherRequest $request)
    {
        // return response()->json(['status' => 2, "message" => "Voucher service has been temporarily suspended"]);
        if ($request->get('qr_code')) {
            $response = $this->qrRepository->checkQrCode($request->get('qr_code'));
            if ($response['status'] === 2) {
                return response()->json(['status' => $response['status'], 'message' => $response['message']]);
            }
        }

        $city = City::findOrFail($request->input('receiver_city_id'));
        if (!$city->is_available_d2d) {
            return response()->json(['status' => 2, 'message' => $city->name . ' city is not available']);
        }
        $request['platform'] = 'Merchant App';
        $voucher = $this->voucherRepository->create($request->all());
        if ($voucher) {
            // create and bind
            if ($request->get('qr_code')) {
                $qr_associate = QrAssociate::where('qr_code', $request->get('qr_code'))->first();
                $qr_bind = $this->qrRepository->bindQR($voucher, $qr_associate);
            }
            return new VoucherResource($voucher->load([
                'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'attachments',
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
        return new VoucherResource($voucher->load([
            'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'attachments',
            'call_status', 'delivery_status', 'store_status', 'parcels', 'pending_returning_actor',
            'pickup', 'pickup.sender', 'pickup.opened_by_staff' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    public function update(UpdateVoucherRequest $request, Voucher $voucher)
    {
        $city = City::findOrFail($request->input('receiver_city_id'));
        if (!$city->is_available_d2d) {
            return response()->json(['status' => 2, 'message' => $city->name . ' city is not available']);
        }
        if ($voucher->is_closed) {
            $remark = request()->only(['remark']);
            if (isset($remark['remark']) && $remark['remark']) {
                $voucher = $this->voucherRepository->update_note($voucher, request()->only(['remark']));
                return response()->json(['status' => 1, "message" => "Successful updated note"]);
            } else {
                return response()->json(['status' => 2, "message" => "Cannot update voucher is already closed or return"]);
            }
        } else {
            $voucher = $this->voucherRepository->update($voucher, $request->all());
            return new VoucherResource($voucher->load([
                'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'attachments',
                'call_status', 'delivery_status', 'store_status','pending_returning_actor' => function ($query) {
                    $query->withTrashed();
                }
            ]));
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

    public function pickupNullVouchers()
    {
        $vouchers = Voucher::WhereNull('pickup_id')
            ->filter(request()->only([
                'search'
            ]))
            ->with([
                'pickup', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'attachments',
                'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration',
                'delegate_person', 'parcels','pending_returning_actor' => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->orderBy('id', 'desc')
            ->paginate(20);
        // $vouchers = Voucher::with('parcels')->paginate(20);

        return new VoucherCollection($vouchers);
        //return new VoucherCollection($vouchers);
    }

    public function filter()
    {
        $vouchers = Voucher::merchantFilter(request()->only([
            'filter', 'search'
        ]))
            ->with([
                'pickup', 'pickup.sender', 'customer', 'payment_type', 'attachments',
                'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
                'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration',
                'delegate_person', 'parcels','pending_returning_actor' => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->orderBy('id', 'desc');
        if (request()->get('filter') == 'draft') {
            $vouchers = $vouchers->get();
        } else {
            $vouchers = $vouchers->paginate(20);
        }

        return new VoucherCollection($vouchers);
    }
    public function search_voucher()
    {
        $vouchers = Voucher::merchantSearch(request()->only(['search']))
            ->where('created_by_id', auth()->user()->id)
            ->where('created_by_type', 'Merchant')
            ->with([
                'pickup', 'pickup.sender', 'customer', 'payment_type', 'attachments',
                'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'delivery_status', 'payment_status' => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->orderBy('id', 'desc')
            ->paginate(20);
        return new VoucherCollection($vouchers);
    }

    public function draftVouchers()
    {
        $vouchers = Voucher::where('created_by_id', auth()->user()->id)
            ->where('created_by_type', 'Merchant')
            ->where('pickup_id', null)
            ->orderBy('id', 'desc')
            ->get();
        return new VoucherCollection($vouchers->load([
            'pickup', 'pickup.sender', 'customer', 'payment_type', 'attachments',
            'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration',
            'delegate_person', 'parcels','pending_returning_actor' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    public function bindedVouchers()
    {
        $vouchers = Voucher::where('created_by_id', auth()->user()->id)
            ->where('created_by_type', 'Merchant')
            ->whereNotNull('qr_associat_id')
            ->whereNotNull('pickup_id')
            ->orderBy('id', 'desc')
            ->get();
        return new VoucherCollection($vouchers->load([
            'pickup', 'pickup.sender', 'customer', 'payment_type', 'attachments',
            'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration',
            'delegate_person', 'parcels','pending_returning_actor' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    /* bind qr code with Voucher */
    public function bindQR(BindQrRequest $request, Voucher $voucher)
    {
        $qr_code = $request->get('qr_code');
        $qr_associate = QrAssociate::where('qr_code', $qr_code)->firstOrFail();

        $response = $this->qrRepository->checkQrCode($request->get('qr_code'));
        if ($response['status'] === 2) {
            return response()->json(['status' => $response['status'], 'message' => $response['message']]);
        }

        $voucher = $this->qrRepository->bindQR($voucher, $qr_associate);

        return response()->json(['status' => 1, "message" => "Ok successful"], Response::HTTP_OK);
    }

    /* unbind with Voucher */
    public function unBindQR(Voucher $voucher)
    {
        $voucher = $this->qrRepository->unBindQR($voucher);

        return response()->json(['status' => 1, "message" => "Ok successful"], Response::HTTP_OK);
    }
}
