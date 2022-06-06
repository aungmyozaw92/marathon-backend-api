<?php

namespace App\Http\Controllers\Web\Api\v1\SuperMerchant;

use App\Models\City;
use App\Models\Zone;
use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\Merchant;
use App\Models\Attachment;
use App\Models\PaymentType;
use Illuminate\Http\Response;
use App\Models\TrackingVoucher;
use App\Models\MerchantAssociate;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperMerchant\CreateVoucherRequest;
use App\Http\Resources\SuperMerchant\Voucher\VoucherResource;
use App\Http\Resources\SuperMerchant\Voucher\VoucherCollection;
use App\Repositories\Web\Api\v1\SuperMerchant\PickupRepository;
use App\Repositories\Web\Api\v1\SuperMerchant\VoucherRepository;
use App\Http\Resources\SuperMerchant\TrackingVoucher\TrackingVoucherCollection;

//use App\Http\Requests\Voucher\UpdateVoucherRequest;

class VoucherController extends Controller
{
    protected $VoucherRepository;

    public function __construct(
        VoucherRepository $VoucherRepository,
        PickupRepository $PickupRepository
    ) {
        $this->voucherRepository = $VoucherRepository;
        $this->pickupRepository = $PickupRepository;
    }

    public function index()
    {
        if (request()->get('merchant_id')) {
            $merchant = Merchant::findOrFail(request()->get('merchant_id'));
            if ($merchant->super_merchant_id != auth()->user()->id) {
                return response()->json([
                    'status' => 2,
                    'message' => "Selected merchant invalid !"
                ]);
            }
        }

        $vouchers = Voucher::superMerchantVoucherFilter(request()->only(['merchant_id']))
            ->with([
                'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone',
                'receiver_zone', 'parcels', 'pickup', 'pickup.sender'  => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->orderBy('id', 'desc')
            ->paginate(25);


        return new VoucherCollection($vouchers);
    }

    public function store(CreateVoucherRequest $request)
    {
        $data = $request->all();
        $merchant = Merchant::findOrFail($data['merchant_id']);
        if ($merchant->super_merchant_id != auth()->user()->id) {
            return response()->json([
                'status' => 2, 'message' => 'Merchant id invalid!'
            ], Response::HTTP_OK);
        }
        $merchant_associate = MerchantAssociate::findOrFail($data['merchant_associate_id']);
        if ($merchant_associate->merchant_id != $merchant->id) {
            return response()->json([
                'status' => 2, 'message' => 'Merchant and merchant\'s associate do not match!'
            ], Response::HTTP_OK);
        }
        $city = City::find($data['receiver_city_id']);
        if (!$merchant->is_root_merchant) {
            if (!$city->is_available_d2d) {
                return response()->json(['status' => 2, 'message' => 'Our service is not available for this destination.']);
            }
        }
        $pickup = Pickup::where('sender_type', 'Merchant')
            ->where('sender_id', $data['merchant_id'])
            ->where('sender_associate_id', $data['merchant_associate_id'])
            ->latest()->first();

        if ($pickup == null || ($pickup && $pickup->pickuped_by_id)) {
            $pickup_data['city_id'] = $merchant->city_id;
            $pickup_data['merchant_id'] = $data['merchant_id'];
            $pickup_data['merchant_associate_id'] = $data['merchant_associate_id'];
            $pickup = $this->pickupRepository->create($pickup_data);
        }

        $data['pickup_id'] = $pickup->id;
        $data['platform'] = 'Supermerchant Platform';
        $response = $this->voucherRepository->create($data);
        if ($response['status'] === 1) {
            return new VoucherResource($response['data']->load([
                'customer', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone', 'parcels',
                'pickup', 'pickup.sender' => function ($query) {
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
                'customer', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone', 'parcels',
                'pickup', 'pickup.sender' => function ($query) {
                    $query->withTrashed();
                }
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
        // get current logged in user
        $user = auth()->user();
        if ($user->can('view', $voucher)) {
            $tracking_vouchers = TrackingVoucher::where('voucher_id', $voucher->id)->with(['tracking_status', 'city'])->get();
            return new TrackingVoucherCollection($tracking_vouchers);
        } else {
            return response()->json([
                'status' => 2,
                'message' => "This action is unauthorized."
            ]);
        }
    }
}
