<?php

namespace App\Http\Controllers\Web\Api\v1\SuperMerchant;

use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\Merchant;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\SuperMerchant\Pickup\PickupResource;
use App\Http\Requests\Thirdparty\Pickup\CreatePickupRequest;
use App\Repositories\Web\Api\v1\ThirdParty\PickupRepository;
use App\Http\Resources\SuperMerchant\Pickup\PickupCollection;

class PickupController extends Controller
{
    /**
     * @var PickupRepository
     */
    protected $pickupRepository;

    /**
     * PickupController constructor.
     *
     * @param PickupRepository $pickupRepository
     */
    public function __construct(PickupRepository $pickupRepository)
    {
        $this->pickupRepository = $pickupRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        $pickups = Pickup::with(
            'sender',
            'vouchers',
            'vouchers.parcels',
            'vouchers.customer',
            'vouchers.sender_city',
            'vouchers.sender_zone',
            'vouchers.receiver_city',
            'vouchers.receiver_zone'
        )
            ->where('sender_type', 'Merchant')
            ->where('created_by_type', 'Merchant')
            ->where('created_by_id', auth()->user()->id);
        if (request()->get('merchant_id')) {
            $pickups = $pickups->where('sender_id', request()->get('merchant_id'));
        }

        $pickups = $pickups->orderBy('id', 'desc')->paginate(25);

        return new PickupCollection($pickups);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePickupRequest $request)
    {
        $merchant = Merchant::find(auth()->user()->id);
        $merchant_associate = $merchant->merchant_associates()->first();

        $vouchers = Voucher::whereIn('id', $request->get('voucher_id'))->get();
        foreach ($vouchers as $voucher) {
            if ($voucher->pickup_id) {
                return response()->json(
                    ['status' => 2, 'message' => "Some voucher is already include in another pickup!"],
                    Response::HTTP_OK
                );
            }
            if (!auth()->user()->can('view', $voucher)) {
                return response()->json(
                    ['status' => 2, 'message' => "Opps! please re-select because there are other voucher of someone else"],
                    Response::HTTP_OK
                );
            }
        }
        $request['platform'] = 'Supermerchant Platform';
        $pickup = $this->pickupRepository->create(array_merge($request->all(), [
            'merchant_associate_id' => $merchant_associate->id,
            'sender_city_id' => $merchant_associate->city_id,
            'sender_zone_id' => $merchant_associate->zone_id
        ]));

        return new PickupResource($pickup->load(['opened_by_staff', 'vouchers', 'vouchers.customer']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function show(Pickup $pickup)
    {
        if (auth()->user()->id === $pickup->created_by_id && $pickup->created_by_type === 'Merchant') {
            return (new PickupResource($pickup->load([
                'sender', 'vouchers', 'vouchers.parcels', 'vouchers.customer',
                'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.receiver_city', 'vouchers.receiver_zone',
            ])));
        }
        return response()->json([
            'status' => 2,
            'message' => "This action is unauthorized."
        ]);
    }
}
