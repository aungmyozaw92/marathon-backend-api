<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\CourierType;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourierType\CourierTypeResource;
use App\Http\Resources\CourierType\CourierTypeCollection;

class CourierTypeController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courierTypes = CourierType::all();

        return new CourierTypeCollection($courierTypes);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CourierType  $courierType
     * @return \Illuminate\Http\Response
     */
    public function show(CourierType $courierType)
    {
        return new CourierTypeResource($courierType);
    }

}
