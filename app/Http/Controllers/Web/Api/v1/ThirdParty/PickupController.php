<?php

namespace App\Http\Controllers\Web\Api\v1\ThirdParty;

use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\Merchant;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Thirdparty\Pickup\PickupResource;
use App\Http\Resources\Thirdparty\Pickup\PickupCollection;
use App\Http\Requests\Thirdparty\Pickup\CreatePickupRequest;
use App\Http\Requests\Thirdparty\Pickup\UpdatePickupRequest;
use App\Repositories\Web\Api\v1\ThirdParty\PickupRepository;
use App\Http\Requests\ThirdParty\Pickup\AddVoucherToPickupRequest;
use App\Http\Requests\ThirdParty\Pickup\RemoveVoucherFromPickupRequest;

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
        $this->middleware('can:view,pickup')->only('show');
        $this->middleware('can:update,pickup')->only('update');
        $this->middleware('can:delete,pickup')->only('destroy');
        $this->middleware('can:view,pickup')->only('add_voucher');
        $this->pickupRepository = $pickupRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pickups = Pickup::with('opened_by_staff', 'sender', 'created_by')
            ->where('sender_type', 'Merchant')
            // ->where('created_by_type', 'Merchant')
            ->where('sender_id', auth()->user()->id)
            ->filter(request()->only([
                'year', 'month', 'day', 'sender_name', 'sender_phone', 'sender_address',
                'opened_by', 'note', 'search', 'is_pickuped'
            ]))
            ->orderBy('id', 'desc');


        // if (request()->has('paginate')) {
        $pickups = $pickups->paginate(25);
        // } else {
        //     $pickups = $pickups->get();
        // }

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
        $request['platform'] = 'Third-party Platform';

        if ($merchant->is_allow_multiple_pickups) {
            $pickup = $this->pickupRepository->create(array_merge($request->all(), [
                'merchant_associate_id' => $merchant_associate->id,
                'sender_city_id' => $merchant_associate->city_id,
                'sender_zone_id' => $merchant_associate->zone_id
            ]));
        }else{
            $pickup = Pickup::where('sender_type', 'Merchant')
                            ->where('sender_id', $merchant->id)
                            ->where('sender_associate_id', $merchant_associate->id)
                            ->whereDate('requested_date', date('Y-m-d'))
                            ->latest()->first();

            if ($pickup == null || ($pickup && $pickup->is_pickuped)) {
                $pickup = $this->pickupRepository->create(array_merge($request->all(), [
                    'merchant_associate_id' => $merchant_associate->id,
                    'sender_city_id' => $merchant_associate->city_id,
                    'sender_zone_id' => $merchant_associate->zone_id
                ]));
            }else{
                $pickup = $this->pickupRepository->update($pickup, $request->all());
            }
        }
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
        return (new PickupResource($pickup->load([
            'sender', 'vouchers', 'opened_by_staff', 'vouchers.parcels', 'vouchers.customer', 'vouchers.payment_type',
            'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.receiver_city', 'vouchers.receiver_zone',
            'vouchers.sender_bus_station', 'vouchers.receiver_bus_station', 'vouchers.sender_gate', 'created_by',
            'vouchers.receiver_gate', 'vouchers.call_status', 'vouchers.delivery_status', 'vouchers.store_status'
        ])));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePickupRequest $request, Pickup $pickup)
    {
        if (!$pickup->is_pickuped) {
            $pickup = $this->pickupRepository->update($pickup, $request->all());

            return new PickupResource($pickup->load(['opened_by_staff', 'vouchers', 'vouchers.customer']));
        }
        return response()->json(['status' => 2, 'message' => "Unauthenticated!"], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function add_voucher(AddVoucherToPickupRequest $request, Pickup $pickup)
    {
        if (!$pickup->is_pickuped) {
            $vouchers = $request->get('voucher_id');
            foreach ($vouchers as $voucher_id) {
                $voucher =  Voucher::findOrFail($voucher_id);
                if ($voucher->pickup_id) {
                    return response()->json(['status' => 2, 'message' => "Cannot add bcoz some voucher is already including in some pickup!"], Response::HTTP_OK);
                }
                if (!auth()->user()->can('view', $voucher)) {
                    return response()->json(
                        ['status' => 2, 'message' => "Opps! please re-select because there are other voucher of someone else"],
                        Response::HTTP_OK
                    );
                }
            }
            $pickup = $this->pickupRepository->add_voucher($pickup, $request->all());

            return new PickupResource($pickup->load(['opened_by_staff', 'vouchers', 'vouchers.customer']));
        } else {
            return response()->json(['status' => 2, 'message' => "This pickup is already pickuped!"], Response::HTTP_OK);
        }

        return response()->json(['status' => 2, 'message' => "Unauthenticated!"], Response::HTTP_OK);
    }

    public function remove_voucher(RemoveVoucherFromPickupRequest $request, Pickup $pickup)
    {
        if (!$pickup->is_pickuped) {
            $vouchers = $request->get('voucher_id');
            foreach ($vouchers as $voucher_id) {
                $voucher =  Voucher::findOrFail($voucher_id);
                if (!$voucher->pickup_id) {
                    return response()->json(['status' => 2, 'message' => "Cannot remove bcoz some voucher does not include in this pickup!"], Response::HTTP_OK);
                }
                if (!auth()->user()->can('view', $voucher)) {
                    return response()->json(
                        ['status' => 2, 'message' => "Opps! please re-select because there are other voucher of someone else"],
                        Response::HTTP_OK
                    );
                }
                if ($voucher->pickup_id != $pickup->id) {
                    return response()->json(['status' => 2, 'message' => "Cannot remove bcoz some voucher does not match with this pickup!"], Response::HTTP_OK);
                }
            }
            $pickup = $this->pickupRepository->remove_voucher($pickup, $request->all());

            return new PickupResource($pickup->load(['opened_by_staff', 'vouchers', 'vouchers.customer']));
        } else {
            return response()->json(['status' => 2, 'message' => "This pickup is already pickuped!"], Response::HTTP_OK);
        }

        return response()->json(['status' => 2, 'message' => "Unauthenticated!"], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllByDate()
    {
        $pickups = Pickup::with('sender', 'opened_by_staff', 'created_by')
            ->where('sender_type', 'Merchant')
            // ->where('created_by_type', 'Merchant')
            ->where('sender_id', auth()->user()->id)
            ->filter(request()->only([
                'year', 'month', 'day', 'sender_name', 'sender_phone', 'sender_address',
                'opened_by', 'note', 'date'
            ]))
            ->orderBy('id', 'desc')
            ->get();

        return new PickupCollection($pickups);
    }

    public function closed(Pickup $pickup)
    {
        if ($pickup->is_closed) {
            return response()->json(['status' => 1, "message" => "Pickup is already closed"]);
        } else {
            $pickup = $this->pickupRepository->closed($pickup);
            return new PickupResource($pickup->load([
                'sender', 'vouchers', 'opened_by_staff', 'vouchers.parcels', 'vouchers.customer', 'vouchers.payment_type',
                'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.receiver_city', 'vouchers.receiver_zone',
                'vouchers.sender_bus_station', 'vouchers.receiver_bus_station', 'vouchers.sender_gate',
                'vouchers.receiver_gate', 'vouchers.call_status', 'vouchers.delivery_status', 'vouchers.store_status'
            ]));
        }
    }
}
