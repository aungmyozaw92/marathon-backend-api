<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

use App\Models\Voucher;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Http\Requests\Mobile\v2\IncompleteVoucher\UpdateIncompletVoucherRequest;
use App\Http\Requests\Mobile\v2\IncompleteVoucher\CreateIncompleteVoucherRequest;
use App\Http\Requests\Mobile\v2\IncompleteVoucher\UpdateReceiverIncompletVoucherRequest;
use App\Repositories\Mobile\Api\v2\Merchant\IncompleteVoucherRepository;
use App\Http\Resources\City\CityCollection;
use App\Http\Resources\Zone\ZoneCollection;
use App\Http\Resources\Parcel\ParcelCollection;
use App\Http\Resources\Mobile\v2\Merchant\IncompleteVoucher\IncompleteVoucherCollection;
use App\Http\Resources\Mobile\v2\Merchant\IncompleteVoucher\IncompleteVoucherResource;
use App\Http\Resources\DashboardTracking\TrackingVoucherCollection;
use App\Http\Resources\Mobile\Merchant\MerchantResource;
use App\Models\TrackingVoucher;
use App\Models\City;
use App\Models\Zone;

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
        $vouchers = Voucher::Where(function ($q) {
			$q->where('created_by_id', auth()->user()->id)
				->where('created_by_type', 'Merchant');
			})
            // ->where('created_by_id', auth()->user()->id)
            // ->where('created_by_type', 'Merchant')
            ->whereNull('pickup_id')
            ->whereNull('receiver_id')
			->with(merchantAppIncompleteVoucherList())
			->orderBy('id', 'desc')
			->paginate(25);
        return new IncompleteVoucherCollection($vouchers);
    }

    public function store(CreateIncompleteVoucherRequest $request)
    {
        $request['platform'] = 'Merchant App';
        $response = $this->incompleteVoucherRepository->create($request->all());
        if (isset($response->uuid)) {
            $deep_link = \Config::get('services.shareable_link.deep_link');
            $url = $deep_link . $response->uuid;
            return response()->json(['status' => 1, 'shareable_url' => preg_replace('/\\\"/', "\"", $url)]);
            // return $this->getUrl($response->uuid);
        }else{
            return response()->json(['status'=>2,'Something Went Wrong!']);
        }
    }
    private function getUrl($uuid) {
        // $uuid = '09090';
        // $url = 'https%3A%2F%2Fmarathonmyanmar.com/fill_info/'.$uuid;
        $url = 'https%3A%2F%2Fmarathonmyanmar.com%2Ffill_info%2F'.$uuid;
        $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://url-shortener-service.p.rapidapi.com/shorten",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                // CURLOPT_POSTFIELDS => "url=https%3A%2F%2Fmarathonmyanmar.com%2F",
                CURLOPT_POSTFIELDS => "url=".$url,
                CURLOPT_HTTPHEADER => [
                    "content-type: application/x-www-form-urlencoded",
                    "x-rapidapi-host: url-shortener-service.p.rapidapi.com",
                    "x-rapidapi-key: 5d5af162demsh06670ad0329d28ap169679jsnd3179f6e0d7f"
                ],
            ]);
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        if ($error) {
            return response($error,500)->header('Content-Type','application/json');
        } else {
            return response($response,200)->header('Content-Type','application/json');
        }
    }
    public function update(UpdateIncompletVoucherRequest $request, Voucher $voucher)
    {
        if ($voucher->is_closed || $voucher->is_picked) {
            return response()->json(['status' => 2, "message" => "Cannot update voucher is already closed or return"]);
        } else {
            $response = $this->incompleteVoucherRepository->update($voucher, $request->all());
            if ($response['status'] == 2) {
                return  $response;
            }
			return new IncompleteVoucherResource($voucher);
        }
    }

    public function update_receiver(UpdateReceiverIncompletVoucherRequest $request, Voucher $voucher)
    {
        if ($voucher->is_closed || $voucher->is_picked) {
            return response()->json(['status' => 2, "message" => "Cannot update voucher is already closed or pickuped"]);
        } else {
            $response = $this->incompleteVoucherRepository->update_receiver($voucher, $request->all());
            if ($response['status'] == 2) {
                return  $response;
            }
            $tracking_vouchers = TrackingVoucher::where('voucher_id', $response->id)->with(['tracking_status', 'city'])->get();
            $parcels = $voucher->parcels()->with('parcel_items', 'parcel_items.product')->get();
			$parcels = new ParcelCollection($parcels);
			$voucher_merchant = (isset($voucher->pickup) && isset($voucher->pickup->merchant)) && $voucher->pickup->merchant ?
				$voucher->pickup->merchant
				: (auth()->user() != null ? auth()->user() : $voucher->created_by_merchant);
            $merchant = new MerchantResource($voucher_merchant);
			$tracking_vouchers = new TrackingVoucherCollection($tracking_vouchers);
			$voucher_info = [
				'receiver_name' => $voucher->receiver_name,
				'receiver_phone' => $voucher->receiver_phone,
				'seller_discount' => $voucher->seller_discount,
				'total_amount_to_collect' => $voucher->total_amount_to_collect,
				'payment_type' => $voucher->payment_type,
				'uuid' => $voucher->uuid
			];
			return response()->json(['status' => 200, 
				'merchant' => $merchant, 
				'parcels' => $parcels, 
				'trackings'=> $tracking_vouchers,
				'voucher_info'=>$voucher_info]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $voucher = Voucher::where('uuid', $uuid)->firstOrFail();
        if ($voucher->receiver_id) {
            $tracking_vouchers = TrackingVoucher::where('voucher_id', $voucher->id)->with(['tracking_status', 'city'])->get();
			$parcels = $voucher->parcels()->with('parcel_items', 'parcel_items.product')->get();
			$voucher_merchant = (isset($voucher->pickup) && isset($voucher->pickup->merchant)) && $voucher->pickup->merchant ?
				$voucher->pickup->merchant
				: (auth()->user() != null ? auth()->user() : $voucher->created_by_merchant);
            $merchant = new MerchantResource($voucher_merchant);
			$parcels = new ParcelCollection($parcels);
			$voucher_info = [
				'receiver_name' => $voucher->receiver_name,
				'receiver_phone' => $voucher->receiver_phone,
				'seller_discount' => $voucher->seller_discount,
				'total_amount_to_collect' => $voucher->total_amount_to_collect,
				'payment_type' => $voucher->payment_type
			];
            $trackings = new TrackingVoucherCollection($tracking_vouchers);
			return response()->json(['status' => 1, 
				'merchant' => $merchant, 
				'parcels' => $parcels, 
				'trackings' => $trackings,
				'voucher_info' => $voucher_info]);
        } else {
			$parcels = $voucher->parcels()->with('parcel_items', 'parcel_items.product')->get();
			$voucher_merchant = (isset($voucher->pickup) && isset($voucher->pickup->merchant)) && $voucher->pickup->merchant ?
				$voucher->pickup->merchant
				: (auth()->user() != null ? auth()->user() : $voucher->created_by_merchant);
            $merchant = new MerchantResource($voucher_merchant);
            $parcels = new ParcelCollection($parcels);
            $cities = new CityCollection(City::where('is_available_d2d', true)->orderBy('name','asc')->get());
            $zones = new ZoneCollection(Zone::where('is_deliver', true)->get());
			return response()->json(['status' => 200, 
				'merchant' => $merchant, 
				'parcels' => $parcels, 
				'cities' => $cities, 
				'zones' => $zones, 
				'seller_discount' => $voucher->seller_discount]);
        }
    }

    public function destroy(Voucher $voucher)
    {
        $this->incompleteVoucherRepository->destroy($voucher);
        return response()->json(['status' => 1], Response::HTTP_OK);
	}
	
	public function redirect_from_link($uuid) {
		$voucher = Voucher::where('uuid', $uuid)->firstOrFail();
		return response()->json(['status'=>1,'id'=>$voucher->id,'document'=>$voucher->firestore_document]);
	}
}
