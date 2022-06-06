<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\Pickup;
use Illuminate\Http\Response;
use App\Exports\MerchantPickupData;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\MerchantDashboard\Pickup\PickupResource;
use App\Http\Resources\MerchantDashboard\Pickup\PickupCollection;
use App\Http\Requests\MerchantDashboard\Pickup\CreatePickupRequest;
use App\Http\Requests\MerchantDashboard\Pickup\UpdatePickupRequest;
use App\Repositories\Web\Api\v1\MerchantDashboard\PickupRepository;

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
        $this->pickupRepository = $pickupRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('export')) {
            $filename = 'merchant_pickups.xlsx';
            Excel::store(new MerchantPickupData, $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/merchant_pickups.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }
        
        $pickups = Pickup::with('opened_by_staff', 'sender', 'created_by')
            ->where('sender_type', 'Merchant')
            // ->where('created_by_type', 'Merchant')
            ->where('sender_id', auth()->user()->id)
            ->filter(request()->only([
                'year', 'month', 'day', 'sender_name', 'sender_phone', 'sender_address',
                'opened_by', 'note', 'search', 'is_pickuped','start_date','end_date',
                 'pickup_start_date','pickup_end_date'
            ]))
            ->orderBy('id', 'desc');


        if (request()->has('paginate')) {
            $pickups = $pickups->paginate(request()->get('paginate'));
        } else {
            $pickups = $pickups->get();
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
        //return response()->json(['status' => 2, "message" => "Calling pickup service has been temporarily suspended"]);
        $request['platform'] = 'Merchant Dashboard';
        $merchant = auth()->user();
        if ($merchant->is_allow_multiple_pickups) {
            $pickup = $this->pickupRepository->create($request->all());
        } else {
            $pickup = Pickup::where('sender_type', 'Merchant')
                            ->where('sender_id', $merchant->id)
                            ->where('sender_associate_id', $request->get('merchant_associate_id'))
                            ->whereDate('requested_date', date('Y-m-d'))
                            ->latest()->first();

            if ($pickup == null || ($pickup && $pickup->is_pickuped)) {
                $pickup = $this->pickupRepository->create($request->all());
            } else {
                $pickup = $this->pickupRepository->update($pickup, $request->all());
            }
        }
        //$pickup = $this->pickupRepository->create($request->all());
        return new PickupResource($pickup->load(['opened_by_staff', 'sender', 'created_by', 'vouchers', 'vouchers.customer']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function show(Pickup $pickup)
    {
        $credit_amount = $pickup->vouchers()->with('journals')->get()->pluck('journals')->collapse()
            ->where('status', 1)
            ->where('credit_account_id', auth()->user()->account->id)
            ->where('debit_account_id', 1)->sum('amount');
        $debit_amount = $pickup->vouchers()->with('journals')->get()->pluck('journals')->collapse()
            ->where('status', 1)
            ->where('credit_account_id', 1)
            ->where('debit_account_id', auth()->user()->account->id)->sum('amount');

        return (new PickupResource($pickup->load([
            'sender', 'vouchers', 'opened_by_staff', 'vouchers.parcels', 'vouchers.customer', 'vouchers.payment_type',
            'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.receiver_city', 'vouchers.receiver_zone',
            'vouchers.sender_bus_station', 'vouchers.receiver_bus_station', 'vouchers.sender_gate', 'created_by',
            'vouchers.receiver_gate', 'vouchers.call_status', 'vouchers.delivery_status', 'vouchers.store_status'
        ])))->additional([
            'total_amount' => $credit_amount - $debit_amount,
        ]);
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pickup $pickup)
    {
        if (!$pickup->is_pickuped) {
            $this->pickupRepository->destroy($pickup);

            return response()->json(['status' => 1], Response::HTTP_OK);
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
