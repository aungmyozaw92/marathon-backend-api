<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\Bus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Bus\BusResource;
use App\Http\Resources\Bus\BusCollection;

class BusController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $buses = $this->busRepository->all();
        $buses = Bus::all();

        return new BusCollection($buses);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bus  $bus
     * @return \Illuminate\Http\Response
     */
    public function show(Bus $bus)
    {
        return new BusResource($bus);
    }
}
