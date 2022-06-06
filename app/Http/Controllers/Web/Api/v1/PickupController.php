<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\City;
use App\Models\Pickup;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Pickup\PickupResource;
use App\Http\Requests\Transaction\FileRequest;
use App\Http\Requests\Pickup\ChangeHeroRequest;
use App\Http\Resources\Pickup\PickupCollection;
use App\Http\Requests\Pickup\CreatePickupRequest;
use App\Http\Requests\Pickup\UpdatePickupRequest;
use App\Repositories\Web\Api\v1\PickupRepository;
use App\Http\Requests\Pickup\UpdatePickedByRequest;
use App\Http\Requests\Pickup\UpdatePickupFeeRequest;
use App\Http\Requests\Pickup\UpdateRequestedDateRequest;

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
        $agent_city_id = request()->get('agent_city_id');
        if ($agent_city_id) {
            $city_id = $agent_city_id;
        } else {
            $city_id = auth()->user()->city_id;
        }

        // $pickups = $this->pickupRepository->paginate();
        if (request()->get('show_correspond_merchant')) {
            $merchants_id = Merchant::where('staff_id', auth()->user()->id)->get()->pluck('id');
            $pickups = Pickup::with('sender', 'sender.staff', 'sender.staff', 'opened_by_staff', 'created_by', 'assigned_by', 'pickuped_by', 'attachments')
                ->filter(request()->only([
                    'year', 'month', 'day', 'sender_name', 'sender_phone', 'sender_address', 'search',
                    'opened_by', 'note', 'date', 'is_closed', 'start_date', 'end_date', 'merchant_id',
                    'created_by_type', 'pickuped_by', 'is_paid', 'pickuped_by_id', 'pickup_invoice', 'is_pickuped',
                    'voucher_count','requested_date'
                ]))
                ->where('sender_type', 'Merchant')
                ->whereIn('sender_id', $merchants_id)
                ->where(function ($query) use ($city_id) {
                    (auth()->user()->hasRole('HQ') || (!request()->has('agent_city_id') && auth()->user()->hasRole('Agent')))  ?  $query : $query->where('city_id', $city_id);
                })
                ->orderBy('id', 'desc')
                ->paginate(25);
        } else {
            $pickups = Pickup::with('sender', 'sender.staff', 'opened_by_staff', 'created_by', 'assigned_by', 'pickuped_by', 'attachments')
                ->filter(request()->only([
                    'year', 'month', 'day', 'sender_name', 'sender_phone', 'sender_address', 'search',
                    'opened_by', 'note', 'date', 'is_closed', 'start_date', 'end_date', 'merchant_id',
                    'created_by_type', 'pickuped_by', 'sender_type', 'is_paid', 'pickuped_by_id', 'pickup_invoice', 'is_pickuped',
                    'voucher_count','requested_date'
                ]))
                ->where(function ($query) use ($city_id) {
                    (auth()->user()->hasRole('HQ') || (!request()->has('agent_city_id') && auth()->user()->hasRole('Agent')))  ?  $query : $query->where('city_id', $city_id);
                })
                ->orderBy('id', 'desc')
                ->paginate(25);
        }
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
        // dd($request->url());
        if(str_contains($request->url(), 'old.marathonmyanmar')){
            return response()->json(
                ['status' => 2, 'message' => 'Cannot create in old url.'],
                Response::HTTP_OK
            );
        }
        if (request()->has('agent_city_id')) {
            $city_id = request()->get('agent_city_id');
            $agent = City::find($city_id)->agent;
            if (!$agent) {
                return response()->json(
                    ['status' => 2, 'message' => 'Pls select another city because This city has not agent.'],
                    Response::HTTP_OK
                );
            }
        }
        $request['platform'] = 'Marathon Dashboard';
        $pickup = $this->pickupRepository->create($request->all());

        return new PickupResource($pickup->load(['sender', 'sender.staff', 'opened_by_staff', 'attachments']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function show(Pickup $pickup)
    {
        // 'vouchers.pickup.sender.staff'

        // $pickup = Pickup::with(['sender','vouchers','vouchers.parcels','vouchers.customer','vouchers.payment_type'
        //                         ,'vouchers.sender_city','vouchers.sender_zone','vouchers.receiver_city','vouchers.receiver_zone'
        //                         ,'vouchers.sender_bus_station','vouchers.receiver_bus_station','vouchers.sender_gate','vouchers.receiver_gate'
        //                         ,'vouchers.call_status','vouchers.delivery_status','vouchers.store_status','vouchers.pickup.sender.staff'
        //                         ,'assigned_by','pickuped_by'
        // ])->find($pickup->id);
        // return new PickupResource($pickup);
        return new PickupResource($pickup->load([
            'sender', 'sender.staff', 'sender.city',
            'sender.account','sender.city.branch',
            'sender_associate.city.branch','sender_associate.zone','sender_associate.city',
            // 'sender_associate.contact_associates',
            'vouchers', 'vouchers.parcels', 
            'vouchers.parcels.parcel_items', 'vouchers.parcels.discount_type','vouchers.parcels.coupon_associate',
            'vouchers.parcels.global_scale', 
            'vouchers.customer', 'vouchers.payment_type','vouchers.store_status',
            'vouchers.receiver_city','vouchers.receiver_city.branch',  'vouchers.receiver_zone',
            // 'vouchers.sender_bus_station', 'vouchers.receiver_bus_station', 
            // 'vouchers.sender_gate', 'created_by''vouchers.receiver_gate', 
            // 'vouchers.call_status', 
            'created_by',
            'vouchers.delivery_status', 
            'vouchers.store_status',
            //  'vouchers.pickup.sender.staff', 
            // 'sender.staff', 
            'assigned_by', 'pickuped_by', 'attachments'
        ]));
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
        $pickup = $this->pickupRepository->update($pickup, $request->all());

        return new PickupResource($pickup->load([
            'sender', 'sender.staff', 'vouchers', 'opened_by_staff', 'vouchers.parcels', 'vouchers.customer', 'vouchers.payment_type',
            'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.receiver_city', 'vouchers.receiver_zone',
            'vouchers.sender_bus_station', 'vouchers.receiver_bus_station', 'vouchers.sender_gate', 'created_by',
            'vouchers.receiver_gate', 'vouchers.call_status', 'vouchers.delivery_status', 'vouchers.store_status',
            'attachments'
        ]));
    }

    public function update_store_status(Pickup $pickup)
    {
        if (($pickup->is_commissionable || $pickup->is_pointable) && !$pickup->is_came_from_mobile && $pickup->actby_mobile == null) {
            return response()->json(['status' => 2, "message" => "Sorry!. Hero has not yet pickup."]);
        } else if ($pickup->assigned_by_id) {
            $pickup = $this->pickupRepository->update_store_status($pickup);
            return response()->json(['status' => 1, "message" => "All store status successfully updated"]);
        } else {
            return response()->json(['status' => 2, "message" => "You need to assign this pickup"]);
        }
        // return new PickupResource($pickup->load([
        //     'sender', 'vouchers', 'opened_by_staff', 'vouchers.parcels', 'vouchers.customer', 'vouchers.payment_type',
        //     'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.receiver_city', 'vouchers.receiver_zone',
        //     'vouchers.sender_bus_station', 'vouchers.receiver_bus_station', 'vouchers.sender_gate', 'created_by',
        //     'vouchers.receiver_gate', 'vouchers.call_status', 'vouchers.delivery_status', 'vouchers.store_status'
        // ]));
    }

    public function update_undo_store_status(Pickup $pickup)
    {
        $pickup = $this->pickupRepository->update_undo_store_status($pickup);
        return response()->json(['status' => 1, "message" => "All store status successfully updated"]);
        // return new PickupResource($pickup->load([
        //     'sender', 'vouchers', 'opened_by_staff', 'vouchers.parcels', 'vouchers.customer', 'vouchers.payment_type',
        //     'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.receiver_city', 'vouchers.receiver_zone',
        //     'vouchers.sender_bus_station', 'vouchers.receiver_bus_station', 'vouchers.sender_gate', 'created_by',
        //     'vouchers.receiver_gate', 'vouchers.call_status', 'vouchers.delivery_status', 'vouchers.store_status'
        // ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pickup $pickup)
    {
        if ($pickup->vouchers->count() > 0) {
            return response()->json(['status' => 2, "message" => "Cannot delete"]);
        }

        $this->pickupRepository->destroy($pickup);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }

    public function closed(ChangeHeroRequest $request, Pickup $pickup)
    {
        if (($pickup->is_commissionable || $pickup->is_pointable) && !$pickup->is_came_from_mobile && $pickup->actby_mobile == null) {
            return response()->json(['status' => 2, "message" => "Sorry!. Hero has not yet pickup."]);
        } else if ($pickup->is_closed) {
            return response()->json(['status' => 2, "message" => "Pickup is already closed"]);
        } else {
            $pickup = $this->pickupRepository->closed($pickup, $request->all());
            return new PickupResource($pickup->load([
                'sender', 'sender.staff', 'vouchers', 'opened_by_staff', 'vouchers.parcels', 'vouchers.customer', 'vouchers.payment_type',
                'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.receiver_city', 'vouchers.receiver_zone',
                'vouchers.sender_bus_station', 'vouchers.receiver_bus_station', 'vouchers.sender_gate',
                'vouchers.receiver_gate', 'vouchers.call_status', 'vouchers.delivery_status', 'vouchers.store_status',
                'attachments'
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

    public function update_requested_date(UpdateRequestedDateRequest $request, Pickup $pickup)
    {
        $pickup = $this->pickupRepository->update_requested_date($pickup, request()->only(['requested_date']));

        if ($pickup) {
            return response()->json([
                'status' => 1, "message" => "Pickup is successfully update requested date"
            ], Response::HTTP_OK);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllByDate()
    {
        $pickups = Pickup::with('sender', 'sender.staff', 'opened_by_staff', 'created_by', 'pickuped_by', 'attachments')
            ->where('city_id', auth()->user()->city_id)
            ->filter(request()->only([
                'year', 'month', 'day', 'sender_name', 'sender_phone', 'sender_address',
                'opened_by', 'note', 'date', 'created_by_type'
            ]))
            ->orderBy('id', 'desc')
            ->get();

        return new PickupCollection($pickups);
    }

    /**
     * Assign Pickup.
     *
     * @return \Illuminate\Http\Response
     */
    public function assign_pickup(UpdatePickedByRequest $request)
    {
        $pickup = $this->pickupRepository->assign_pickup($request->all());

        return new PickupResource($pickup->load([
            'sender', 'sender.staff', 'vouchers', 'opened_by_staff', 'vouchers.parcels', 'created_by',
            'vouchers.customer', 'vouchers.payment_type', 'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.receiver_city', 'vouchers.receiver_zone',
            'vouchers.sender_bus_station', 'vouchers.receiver_bus_station', 'vouchers.sender_gate', 'vouchers.receiver_gate',
            'vouchers.call_status', 'vouchers.delivery_status', 'vouchers.store_status', 'pickuped_by', 'attachments'
        ]));

        // return response()->json([
        //     'status' => 1 , "message" => "Delivery is successfully assigned to pickup."
        // ], Response::HTTP_OK);
    }

    /**
     * Upload Pickup's Image
     */
    public function upload(Pickup $pickup, FileRequest $request)
    {
        if ($request->hasFile('file') && $file = $request->file('file')) {
            $pickup = $this->pickupRepository->upload($pickup, $file);
        }

        return new PickupResource($pickup->load([
            'sender', 'sender.staff', 'vouchers', 'opened_by_staff', 'vouchers.parcels', 'created_by',
            'vouchers.customer', 'vouchers.payment_type', 'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.receiver_city', 'vouchers.receiver_zone',
            'vouchers.sender_bus_station', 'vouchers.receiver_bus_station', 'vouchers.sender_gate', 'vouchers.receiver_gate',
            'vouchers.call_status', 'vouchers.delivery_status', 'vouchers.store_status', 'pickuped_by', 'attachments'
        ]));
    }
    public function change_hero(ChangeHeroRequest $request)
    {
        $pickup = Pickup::find($request->get('pickup_id'));

        if (!$pickup->is_closed) {
            $pickup = $this->pickupRepository->change_hero($pickup, $request->all());

            return new PickupResource($pickup->load([
                'sender', 'sender.staff', 'vouchers', 'opened_by_staff', 'vouchers.parcels', 'created_by',
                'vouchers.customer', 'vouchers.payment_type', 'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.receiver_city', 'vouchers.receiver_zone',
                'vouchers.sender_bus_station', 'vouchers.receiver_bus_station', 'vouchers.sender_gate', 'vouchers.receiver_gate',
                'vouchers.call_status', 'vouchers.delivery_status', 'vouchers.store_status', 'pickuped_by', 'attachments'
            ]));
        }
        return response()->json(['status' => 2, 'message' => 'Pickup is already closed.'], Response::HTTP_OK);
    }
}
