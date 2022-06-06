<?php

namespace App\Http\Controllers\Web\Api\v1\ThirdParty;

use App\Models\City;
use App\Models\Zone;
use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\Merchant;
use App\Models\Attachment;
use App\Models\PaymentType;
use Illuminate\Http\Response;
use App\Models\TrackingVoucher;
use App\Http\Controllers\Controller;
use App\Http\Requests\ThirdParty\CreateVoucherRequest;
use App\Http\Resources\ThirdParty\Voucher\VoucherResource;
use App\Http\Resources\ThirdParty\Voucher\VoucherCollection;
use App\Repositories\Web\Api\v1\MerchantDashboard\VoucherRepository;
use App\Http\Resources\ThirdParty\TrackingVoucher\TrackingVoucherCollection;
use App\Http\Resources\SuperMerchant\TrackingVoucher\TrackingVoucherResource;
use App\Http\Resources\ThirdParty\LatestTrackingVoucher\LatestTrackingVoucherCollection;

//use App\Http\Requests\Voucher\UpdateVoucherRequest;

class VoucherController extends Controller
{
    protected $VoucherRepository;

    public function __construct(VoucherRepository $VoucherRepository)
    {
        $this->voucherRepository = $VoucherRepository;
    }

    public function index()
    {
        $vouchers = Voucher::where(function ($q) {
            $q->where('created_by_id', auth()->user()->id)
                ->where('created_by_type', 'Merchant')
                ->whereNull('pickup_id');
        })
            ->with([
                'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone',
                'receiver_zone', 'sender_bus_station', 'receiver_bus_station', 'sender_gate',
                'receiver_gate', 'call_status', 'delivery_status', 'store_status',
                'payment_status', 'parcels','pending_returning_actor' => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->orderBy('id', 'desc')
            ->paginate(25);

        return new VoucherCollection($vouchers);
    }

    public function get_latest_tracking_vouchers()
    {
        $orderBy = (request()->has('orderBy') && request()->get('orderBy'))? request()->get('orderBy') : 'asc';
        $vouchers = Voucher::where(function ($q) {
            $q->where('created_by_id', auth()->user()->id)
                ->where('created_by_type', 'Merchant')
                ->whereNotNull('pickup_id');
        })
            ->orderBy('id', $orderBy);
        
        if (request()->has(['paginate'])) {
            $vouchers = $vouchers->paginate(request()->get('paginate'));
        }else{
            $vouchers = $vouchers->get();
        }

        return new LatestTrackingVoucherCollection($vouchers);
    }

    public function store(CreateVoucherRequest $request)
    {
        $data = $request->all();
        $merchant = Merchant::findOrFail(auth()->user()->id);
        $data['payment_type_id'] = isset($data['payment_type_id']) ? $data['payment_type_id'] : 4;
        $data['sender_city_id'] = $merchant->city_id;
        $data['sender_id'] = $merchant->id;
        $data['platform'] = 'Third-party Platform';
        $city = City::find($data['receiver_city_id']);
        if (!$merchant->is_root_merchant) {
            if (!$city->is_available_d2d) {
                return response()->json(['status' => 2, 'message' => 'Our service is not available for this destination.']);
            }
        }
        $response = $this->voucherRepository->create($data);
        if ($response['status'] === 1) {
            return new VoucherResource($response['data']->load([
                'customer','payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
                'call_status', 'delivery_status', 'store_status', 'parcels',
                'pickup', 'pickup.sender', 'pickup.opened_by_staff' => function ($query) {
                    $query->withTrashed();
                }
            ]));
        } else {
            return response()->json(['status' => $response['status'], 'message' => $response['message']]);
        }
    }

    public function show(Voucher $voucher)
    {
        // get current logged in user
        $user = auth()->user();
        if ($user->can('view', $voucher)) {
            return new VoucherResource($voucher->load([
                'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
                'call_status', 'delivery_status', 'store_status', 'parcels',
                'pickup', 'pickup.sender', 'pickup.opened_by_staff','pending_returning_actor' => function ($query) {
                    $query->withTrashed();
                }, 'attachments'
            ]));
        } else {
            return response()->json([
                'status' => 2,
                'message' => "This action is unauthorized."
            ]);
        }
    }

    public function voucher_trackings(Voucher $voucher)
    {
        // dd($voucher->tracking_vouchers);
        // get current logged in user
        $user = auth()->user();
        $orderBy = (request()->has('orderBy') && request()->get('orderBy'))? request()->get('orderBy') : 'asc';
        
        if ($user->can('view', $voucher)) {
            $tracking_vouchers = TrackingVoucher::where('voucher_id', $voucher->id)
                                            ->with(['tracking_status', 'city'])
                                            ->orderBy('id', $orderBy)
                                            ->get();
            return new TrackingVoucherCollection($tracking_vouchers);
        } else {
            return response()->json([
                'status' => 2,
                'message' => "This action is unauthorized."
            ]);
        }
    }
}
