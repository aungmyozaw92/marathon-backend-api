<?php

namespace App\Http\Controllers\Mobile\Api\v1\Operation;

use App\Models\Pickup;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\PickupRepository;
use App\Http\Requests\Pickup\UpdatePickupFeeRequest;
use App\Http\Requests\Pickup\CreatePickupRequest;
use App\Http\Resources\Mobile\Operation\Pickup\PickupResource;
use App\Http\Resources\Mobile\Operation\Pickup\PickupCollection;

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
        $pickups = Pickup::with('opened_by_staff')
            ->operationFilter(request()->only([
                'merchant_id', 'delivery_id'
            ]))
            ->orderBy('id', 'desc')
            ->paginate(20);

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
        $pickup = $this->pickupRepository->create($request->all());

        return new PickupResource($pickup->load(['opened_by_staff', 'vouchers', 'vouchers.customer', 'sender']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function show(Pickup $pickup)
    {
        return new PickupResource($pickup->load([
            'sender', 'vouchers', 'opened_by_staff', 'vouchers.parcels', 'vouchers.customer', 'vouchers.payment_type',
            'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.receiver_city', 'vouchers.receiver_zone',
            'vouchers.sender_bus_station', 'vouchers.receiver_bus_station', 'vouchers.sender_gate', 'created_by',
            'vouchers.receiver_gate', 'vouchers.call_status', 'vouchers.delivery_status', 'vouchers.store_status'
        ]));
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

    public function update_pickup_fee(UpdatePickupFeeRequest $request, Pickup $pickup)
    {
        $pickup = $this->pickupRepository->update_pickup_fee($pickup, request()->only(['take_pickup_fee']));

        if ($pickup) {
            return response()->json([
                'status' => 1, "message" => "Pickup is successfully update pickup fee"
            ], Response::HTTP_OK);
        }
    }
}
