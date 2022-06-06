<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

use App\Models\Pickup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\v2\Merchant\Pickup\PickupResource;
use App\Http\Resources\Mobile\v2\Merchant\Pickup\PickupCollection;
use App\Repositories\Mobile\Api\v1\PickupRepository;
use App\Http\Requests\Mobile\v2\Pickup\CreatePickupRequest;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function index() {
		$pickups = Pickup::Where(function ($q) {
			$q->where('created_by_id', auth()->user()->id)
				->where('created_by_type', 'Merchant');
			})->orderBy('id', 'desc')
			->paginate(25);
		return new PickupCollection($pickups);
	}
    public function store(CreatePickupRequest $request)
    {
        //return response()->json(['status' => 2, "message" => "Calling pickup service has been temporarily suspended"]);
        $request['platform'] = 'Merchant App';
        $merchant = auth()->user();
        if ($merchant->is_allow_multiple_pickups) {
            $pickup = $this->pickupRepository->create($request->all());
        } else {
            $pickup = Pickup::where('sender_type', 'Merchant')
                ->where('sender_id', $merchant->id)
                ->where('sender_associate_id', $request->get('merchant_associate_id'))
                // ->whereDate('requested_date', date('Y-m-d'))
                ->whereDate('requested_date', $request->requested_date)
                ->latest()->first();

            if ($pickup == null || ($pickup && $pickup->is_pickuped)) {
                $pickup = $this->pickupRepository->create($request->all());
            } else {
                $pickup = $this->pickupRepository->update($pickup, $request->all());
            }
        }
        return new PickupResource($pickup);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pickup $pickup)
    {
        if (
            $pickup->is_pickuped ||
            ($pickup->created_by_type != 'Merchant' || $pickup->created_by_id != auth()->user()->id)
        ) {
            $note = request()->only(['note']);
            if (isset($note['note']) && $note['note']) {
                $pickup = $this->pickupRepository->update_note($pickup, request()->only(['note']));
                return response()->json(['status' => 1, "message" => "Successful updated note"]);
            } else {
                return response()->json(['status' => 2, "message" => "Pickup is lready picked or operating by marathon dashboard."]);
            }
        }
        $pickup = $this->pickupRepository->update($pickup, $request->all());
        if ($pickup) {
            return response()->json(['status' => 1, 'message' => 'Successfully Updated!'], Response::HTTP_OK);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pickup $pickup)
    {
		if (
			$pickup->is_pickuped || ($pickup->created_by_type != 'Merchant' || $pickup->created_by_id != auth()->user()->id)
		){
			return response()->json(['status' => 2, "message" => "Pickup is lready picked or operating by marathon dashboard."]);
		}
        if ($pickup->vouchers->count() > 0) {
            return response()->json(['status' => 2, "message" => "Cannot delete"]);
        }
        $this->pickupRepository->destroy($pickup);

        return response()->json(['status' => 1, 'message' => 'Successfully Updated!'], Response::HTTP_OK);
    }
}
