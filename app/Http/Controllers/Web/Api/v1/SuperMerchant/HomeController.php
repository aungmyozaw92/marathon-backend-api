<?php

namespace App\Http\Controllers\Web\Api\v1\SuperMerchant;

use App\Models\Bank;
use App\Models\City;
use App\Models\Zone;
//use App\Http\Requests\Mobile\CalculateAmount\CalculateAmountRequest;
use App\Models\Route;
use App\Models\GlobalScale;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Bank\BankCollection;
use App\Http\Requests\SuperMerchant\CheckRouteRequest;
use App\Http\Resources\GlobalScale\GlobalScaleCollection;
use App\Http\Resources\PaymentType\PaymentTypeCollection;
use App\Http\Resources\SuperMerchant\City\CityCollection;
use App\Http\Resources\SuperMerchant\Zone\ZoneCollection;
use App\Http\Requests\SuperMerchant\DeliveryAmountRequest;
use App\Http\Requests\SuperMerchant\DeliveryAmountDetailRequest;
use App\Repositories\Web\Api\v1\SuperMerchant\CalculateAmountRepository;


class HomeController extends Controller
{
    /**
     * @var CalculateAmountRepository
     */
    protected $calculateAmountRepository;

    /**
     * CalculateAmountontroller constructor.
     *
     * @param CalculateAmountRepository $calculateAmountRepository
     */
    public function __construct(CalculateAmountRepository $calculateAmountRepository)
    {
        $this->calculateAmountRepository = $calculateAmountRepository;
    }

    /**
     * Calculate total delivery amount 
     *
     * @return \Illuminate\Http\Response
     */
    public function calculate_price(DeliveryAmountRequest $request)
    {
        $calculate_amount = $this->calculateAmountRepository->calculate_delivery_amount($request->all());

        return $calculate_amount;
    }

    /**
     * Calculate total delivery amount 
     *
     * @return \Illuminate\Http\Response
     */
    public function calculate_price_detail(DeliveryAmountDetailRequest $request)
    {
        $calculate_amount = $this->calculateAmountRepository->calculate_delivery_amount($request->all());

        return $calculate_amount;
    }

    public function check_route(CheckRouteRequest $request)
    {
        $city = City::find($request->get('receiver_city_id'));
        $route = null;
        if ($city->is_available_d2d) {
            $route = Route::where('origin_id', auth()->user()->city_id)
                      ->where('destination_id', $request->get('receiver_city_id'))->first();
        }
        if($route){
            return response()->json([
                'status' => 1,
                "message" => [
                    "receiver_city_id" => $request->get('receiver_city_id'), 
                    "delivered" => true,
                    "note" => "The selected receiver city is valid."
                ],
            ], Response::HTTP_OK);
        }else{
            return response()->json([
                'status' => 2,
                "message" => [
                    "receiver_city_id" => $request->get('receiver_city_id'), 
                    "delivered" => false,
                    "note" => "The selected receiver city is invalid."
                ],
            ], Response::HTTP_OK);
        }
    }

    public function get_master_records()
    {

        $cities = City::with(['zones' => function($q)  {
                                $q->where('is_deliver', '=', 1);
                            }])
                            ->where('is_available_d2d',1)
                            ->get();
        $zones = Zone::with(['city' => function($q){
                    $q->where('is_available_d2d',1);
                }])
                ->where('is_deliver',1)->get();
        $global_scales = GlobalScale::all();
        $payment_types = PaymentType::whereIn('id',[1,2,3,4,9,10])->get();
        $banks = Bank::all();

        return response()->json([
            'status' => 1,
            'data' => [
                'cities' => new CityCollection($cities),
                'zones' => new ZoneCollection($zones),
                'global_scales' => new GlobalScaleCollection($global_scales),                
                'payment_types' => new PaymentTypeCollection($payment_types),                
                'banks' => new BankCollection($banks),                
            ],
        ], 200);
    }
}

