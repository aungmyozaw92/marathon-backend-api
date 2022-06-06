<?php

namespace App\Http\Controllers\Mobile\Api\v1\Operation;

use App\Models\Delivery;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Operation\Staff\StaffResource;
use App\Http\Resources\Mobile\Operation\Staff\StaffCollection;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deliverys = Delivery::all();

        return new StaffCollection($deliverys->load('department', 'zone', 'courier_type'));
    }

    
    /**
     * Display the specified resource.
     *
     * @param  \App\Delivery  $delivery
     * @return \Illuminate\Http\Response
     */
    public function show(Delivery $delivery)
    {
        return new StaffResource($delivery->load('department', 'zone', 'courier_type'));
    }
}
