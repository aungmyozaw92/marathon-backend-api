<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;


use App\Models\City;
use App\Http\Controllers\Controller;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\City\CityCollection;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $cities = $this->cityRepository->all();
       if (auth()->user()->is_root_merchant) {
        $cities = City::with('zones')->get();
       }else{
        $cities = City::with(['zones' => function($q)  {
                $q->where('is_deliver', '=', 1);
            }])
            ->where('is_available_d2d',1)
            ->filter(request()->all())
            ->get();
       }

        return new CityCollection($cities);
    }

   
    /**
     * Display the specified resource.
     *
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
        return new CityResource($city->load(['zones']));
    }

}
